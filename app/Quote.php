<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

/**
 * Class Quote
 * @package App
 */
class Quote extends Model
{
    /**
     * @var array
     */
    private $quotes = [
        'Pega na minha e balança.',
        'O que não mata, engorda.',
        'Onde se ganha o pão não se come a carne.',
    ];

    /**
     * @return mixed
     * @throws \Exception
     */
    public function randomQuote()
    {
        try {
            $index = random_int(0, \count($this->quotes) - 1 );
            $quote = $this->quotes[$index];
        } catch (\Exception $ex) {
            $quote = '';
        }

        return $quote;
    }

    /**
     * @param $quote
     * @return string
     * @throws \ImagickException
     */
    public function generateImage($quote)
    {
        $imgTemplate = public_path('img/suntzu-template.png');
        $imgBackground = public_path('img/bg-text.png');

        $imgText = new \Imagick($imgBackground);
        $imgSunTzu = new \Imagick($imgTemplate);

        $draw = new \ImagickDraw();
        $draw->setGravity(\Imagick::GRAVITY_NORTHWEST);
        $maxHeight = 120;

        $this->fitImageAnnotation($imgText, $draw, $quote, $maxHeight);

        $imgSunTzu->compositeImage($imgText->getImage(), \Imagick::COMPOSITE_COPY,  25, 25);

        return $imgSunTzu->getImageBlob();
    }

    /**
     * @param $quote
     * @return string
     * @throws \ImagickException
     */
    public function generateImageTelegram($quote)
    {
        $path = storage_path('app/frase.png');
        $image64 = $this->generateImage($quote);
        Image::make($image64)->save($path);

        return $path;
    }

    /**
     * @param \Imagick $image
     * @param \ImagickDraw $draw
     * @param $text
     * @param $maxHeight
     * @param int $leading
     * @param float $strokeWidth
     * @param array $margins
     */
    private function fitImageAnnotation(\Imagick $image, \ImagickDraw $draw, $text, $maxHeight, $leading = 1, $strokeWidth = 0.04, $margins =  [10, 10, 10, 10])
    {
        if(\strlen($text) < 1) {
            return;
        }

        $imageWidth = $image->getImageWidth();
        $imageHeight = $image->getImageHeight();

        // margins are css-type margins: T, R, B, L
        $boundingBoxWidth = $imageWidth - $margins[ 1 ] - $margins[ 3 ];
        $boundingBoxHeight = $imageHeight - $margins[ 0 ] - $margins[ 2 ];

        // We begin by setting the initial font size
        // to the maximum allowed height, and work our way down
        $fontSize = $maxHeight;

        $textLength = strlen($text);

        // Start the main routine where the culprits are
        do
        {
            $probeText = $text;
            $probeTextLength = $textLength;
            $lines = explode( "\n", $probeText );
            $lineCount = count($lines);

            $draw->setFontSize($fontSize);
            $draw->setStrokeWidth($fontSize * $strokeWidth);
            $fontMetrics = $image->queryFontMetrics($draw, $probeText, true);

            // This routine will try to wordwrap() text until it
            // finds the ideal distibution of words over lines,
            // given the current font size, to fit the bounding box width
            // If it can't, it will fall through and the parent
            // enclosing routine will try a smaller font size
            while( $fontMetrics[ 'textWidth' ] >= $boundingBoxWidth )
            {

                // While there's no change in line lengths
                // decrease wordwrap length (no point in
                // querying font metrics if the dimensions
                // haven't changed)
                $lineLengths = array_map( 'strlen', $lines );
                do
                {
                    $probeText = wordwrap( $text, $probeTextLength );
                    $lines = explode( "\n", $probeText );

                    // This is one of the performance culprits
                    // I was hoping to find some kind of binary
                    // search type algorithm that eliminates
                    // the need to decrease the length only
                    // one character at a time
                    $probeTextLength--;
                }
                while( $lineLengths === array_map( 'strlen', $lines ) && $probeTextLength > 0 );

                // Get the font metrics for the current line distribution
                $fontMetrics = $image->queryFontMetrics($draw, $probeText, true);

                if($probeTextLength <= 0) {
                    break;
                }
            }

            // Ignore font metrics textHeight, we'll calculate our own
            // based on our $leading argument
            $lineHeight = $leading * $fontSize;
            $lineSpacing = ($leading - 1) * $fontSize;
            $lineCount = \count( $lines );
            $textHeight = ($lineCount * $fontSize) + (($lineCount - 1) * $lineSpacing);


            // This is the other performance culprit
            // Here I was also hoping to find some kind of
            // binary search type algorithm that eliminates
            // the need to decrease the font size only
            // one pixel at a time
            $fontSize -= 1;
        }
        while($textHeight >= $maxHeight || $fontMetrics[ 'textWidth' ] >= $boundingBoxWidth);

        // The remaining part is no culprit, it just draws the final text
        // based on our calculated parameters
        $fontSize = $draw->getFontSize();
        $gravity = $draw->getGravity();

        if( $gravity < \Imagick::GRAVITY_WEST ) {
            $y = $margins[ 0 ] + $fontSize + $fontMetrics[ 'descender' ];
        } else if( $gravity < \Imagick::GRAVITY_SOUTHWEST ) {
            $y = $margins[ 0 ] + ( $boundingBoxHeight / 2 ) - ( $textHeight / 2 ) + $fontSize + $fontMetrics[ 'descender' ];
        } else {
            $y = ( $imageHeight - $textHeight - $margins[ 2 ] ) + $fontSize;
        }

        $alignment = $gravity - floor( ( $gravity - .5 ) / 3 ) * 3;
        if((int)$alignment === \Imagick::ALIGN_LEFT) {
            $x = $margins[3];
        } else if ((int)$alignment === \Imagick::ALIGN_CENTER) {
            $x = $margins[ 3 ] + ( $boundingBoxWidth / 2 );
        } else {
            $x = $imageWidth - $margins[ 1 ];
        }

        $draw->setTextAlignment($alignment);
        $draw->setFillColor("white");
        $draw->setGravity(0);

        foreach($lines as $line)
        {
            $image->annotateImage($draw, $x, $y,0, $line);
            $y += $lineHeight;
        }

        $image->annotateImage($draw, 10, $y + 25, 0, '- Sun Tzu');
    }
}
