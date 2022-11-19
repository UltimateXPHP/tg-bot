<?php

declare(strict_types=1);

namespace App\Service;

use danog\MadelineProto\EventHandler;

class TelegramClient extends EventHandler
{
    /**
     * @var int|string Username or ID of bot admin
     */
    public const ADMIN = "capricornusx";

    /**
     * List of properties automatically stored in database (MySQL, Postgres, redis or memory).
     * @see https://docs.madelineproto.xyz/docs/DATABASE.html
     */
    protected static array $dbProperties = [
        'dataStoredOnDb' => 'array'
    ];

    /**
     * @var DbArray<array>
     */
    protected $dataStoredOnDb;

    /**
     * Get peer(s) where to report errors
     */
    public function getReportPeers(): array
    {
        return [self::ADMIN];
    }
    /**
     * Called on startup, can contain async calls for initialization of the bot
     */
    public function onStart(): void
    {
    }

    /**
     * Handle updates from supergroups and channels
     *
     * @param array $update Update
     * @throws \JsonException
     */
    public function onUpdateNewChannelMessage(array $update): \Generator
    {
        return $this->onUpdateNewMessage($update);
    }

    /**
     * Handle updates from users.
     *
     * @param array $update Update
     *
     * @return \Generator
     * @throws \JsonException
     */
    public function onUpdateNewMessage(array $update): \Generator
    {
        if ($update['message']['_'] === 'messageEmpty' || $update['message']['out'] ?? false) {
            return;
        }
        $res = \json_encode($update, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);

        yield $this->messages->sendMessage(['peer' => $update, 'message' => "<code>$res</code>", 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
        if (isset($update['message']['media']) && $update['message']['media']['_'] !== 'messageMediaGame') {
            yield $this->messages->sendMedia(['peer' => $update, 'message' => $update['message']['message'], 'media' => $update]);
        }

        // You can also use the built-in MadelineProto MySQL async driver!
        // Can be anything serializable, an array, an int, an object
        $myData = [];

        // Use the isset method to check whether some data exists in the database
        if (yield $this->dataStoredOnDb->isset('yourKey')) {
            // Always yield when fetching data
            $myData = yield $this->dataStoredOnDb['yourKey'];
        }
        $this->dataStoredOnDb['yourKey'] = $myData + ['moreStuff' => 'yay'];

        $this->dataStoredOnDb['otherKey'] = 0;
        unset($this->dataStoredOnDb['otherKey']);

        $this->logger("Count: ".(yield $this->dataStoredOnDb->count()));

        // You can even use an async iterator to iterate over the data
        $iterator = $this->dataStoredOnDb->getIterator();
        while (yield $iterator->advance()) {
            [$key, $value] = $iterator->getCurrent();
            $this->logger($key);
            $this->logger($value);
        }
    }
}

