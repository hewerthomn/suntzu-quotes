<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

use App\Quote;

class SunTzuCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'sun_tzu_quote';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commando para gerar uma quote do Sun Tzu';

    /**
     * @param $arguments
     * @return mixed|void
     * @throws \ImagickException
     */
    public function handle($arguments)
    {
        $quote = new Quote;

        if (!$arguments) {
            $arguments = $quote->randomQuote();
        }

        $imagePath = $quote->generateImageTelegram($arguments);
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        $this->replyWithPhoto(['photo' => $imagePath]);
    }
}

