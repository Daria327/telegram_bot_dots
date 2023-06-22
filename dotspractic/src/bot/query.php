<?php

require_once ( __DIR__ . '/../vendor/autoload.php' );
require_once ( __DIR__ . '/../config.php' );
require_once ( __DIR__ . '/Menu.php' );


use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Update;

use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;

function getRequest(Update $webHook) : array {
    return [
        'text' => $webHook['message']['text'],
        'chat_id' => $webHook['message']['chat']['id'],
        'full_name' => $webHook['message']['from']['first_name'] . ' ' . $webHook['message']['from']['last_name'],
    ];
}

/**
 * @throws TelegramSDKException
 */
function sendMessage(Api $bot, string $chat_id, string $text, Keyboard $menu) : void {
    $bot->sendMessage([
        'chat_id' => $chat_id,
        'text' => $text,
        'reply_markup' => $menu
    ]);
}