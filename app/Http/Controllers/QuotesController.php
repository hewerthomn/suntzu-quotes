<?php

namespace App\Http\Controllers;

use App\Quote;

/**
 * Class QuotesController
 * @package App\Http\Controllers
 */
class QuotesController extends Controller
{
    /**
     * @var Quote
     */
    protected $quote;

    /**
     * QuotesController constructor.
     * @param Quote $quote
     */
    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $v['title'] = 'Sun Tzu quotes';

        return view('quotes.index', $v);
    }

    /**
     * @return \Illuminate\Http\Response
     * @throws \ImagickException
     */
    public function show()
    {
        $quote = request()->filled('quote') ? request('quote') : $this->quote->randomQuote();
        $image = $this->quote->generateImage($quote);

        return response()->make($image, 200, [
            'Content-Type' => 'image/png'
        ]);
    }
}
