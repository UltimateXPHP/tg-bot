<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Telegram\Commands\SystemCommands\CallbackCommand;
use App\Domain\Telegram\Commands\UserCommands\StartCommand;
use App\Domain\Telegram\Commands\UserCommands\TestCommand;
use App\Domain\Telegram\Commands\UserCommands\YoutubeCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

use function Lib\Functions\env;

class TelegramService
{
    private Request $request;
    private Telegram $telegram;
    public const MAIN_ADMIN = 103461321; //@capricornusx

    public function __construct(
        private string $apiKey,
        private string $botUsername,
        private string $hookUrl,
        private string $downloadPath,
        private string $uploadPath,
        private string $commandsPath,
    )
    {
        $this->telegram = new Telegram(
            api_key: $this->apiKey,
            bot_username: $this->botUsername
        );

        $this->setLogger();

        $mysqlCredentials = [
            'host'     => 'db2',
            'port'     => 3306,
            'user'     => 'user',
            'password' => 'password',
            'database' => 'hookbot',
        ];
        $this->telegram->enableAdmins([self::MAIN_ADMIN]);
        $this->telegram->enableMySql($mysqlCredentials);

        $this->telegram->setDownloadPath($this->downloadPath);
        $this->telegram->setUploadPath($this->uploadPath);
        $this->telegram->setCommandsPath($this->commandsPath);
        $this->telegram->addCommandClasses([
            StartCommand::class,
            TestCommand::class,
            YoutubeCommand::class,
            CallbackCommand::class,
        ]);

        $this->request = new Request();
        $this->request::initialize($this->telegram);
    }
    
    public function getRequest(): Request
    {
        return $this->request;
    }
    
    public function getTelegram(): Telegram
    {
        return $this->telegram;
    }

    public function getHookUrl(): string
    {
        return $this->hookUrl;
    }

    //todo есть смысл подключить уже существующий монолог $logger
    public function setLogger(): void
    {
        TelegramLog::initialize(
        // Main logger that handles all 'debug' and 'error' logs.
            new Logger('telegram_bot', [
                (new StreamHandler(env('DEBUG_LOG_FILE'), Logger::DEBUG))->setFormatter(new JsonFormatter()),
                (new StreamHandler(env('ERROR_LOG_FILE'), Logger::ERROR))->setFormatter(new JsonFormatter()),
            ]),
//            // Updates logger for raw updates.
//            new Logger('telegram_bot_updates', [
//                (new StreamHandler('var/log/prod/updates_log_file', Logger::INFO))->setFormatter(new JsonFormatter()),
//            ])
        );
    }


}