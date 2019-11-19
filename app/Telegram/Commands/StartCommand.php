<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commando Inicial';

    /**
     * @return mixed|void
     */
    public function handle($arguments)
    {
        $this->replyWithMessage([
            'text' => 'Olá! Bem Vindo ao Sun Tzu bot, aqui a nossa lista de comandos disponíveis:'
        ]);
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $commands = $this->getTelegram()->getCommands();

        $response = '';
        foreach ($commands as $name => $command) {
            $response .= sprintf('/%s - %s' . PHP_EOL, $name, $command->getDescription());
        }

        $this->replyWithMessage(['text' => $response]);
    }
}
