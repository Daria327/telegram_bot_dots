<?php

require_once ( __DIR__ . '/init.php' );

use Telegram\Bot\Api;
use bot\Menu;
use Telegram\Bot\Exceptions\TelegramSDKException;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bot = new Api(BOT_API);

    $menu = new Menu();

    $webHook = $bot->getWebhookUpdate();

    $request = getRequest($webHook);

    switch ($request['text']) {
        case '/start':
        case 'Come back':
            createUser($request['full_name'],$request['chat_id']);
            $text = 'Hi ' . $request['full_name'] . '! This test bot is designed to interact with Dots API services during the practice course.';
            $menu->setMenu([['Choose a city']]);
            break;
        case 'Choose a city':
            $text = 'Select city';
            $menu->setMenu(getCitiesList());
            break;
        case citySearch($request['text']):
            $text = 'City selected, go back or select company';
            $menu->setMenu([['Choose a company', 'Come back']]);
            selectCity($request['chat_id'],$request['text']);
            break;
        case $request['text'] === 'Choose a company' && getCityName($request['chat_id']) === '':
            $text = 'Sorry, first you need to select a city';
            $menu->setMenu([['Choose a city']]);
            break;
        case 'Choose a company':
            $text = 'Choose a company to order';
            $menu->setMenu(getCompanyList(getCityName($request['chat_id'])));
            break;
        case companySearch($request['text'],$request['chat_id']):
            $text = 'Company selected, select items to order or go back';
            $menu->setMenu([['Menu', 'Come back']]);
            selectCompany($request['chat_id'],$request['text']);
            break;
        case ($request['text'] === 'Menu' || $request['text'] === 'Select products' ||
            $request['text'] === 'Send an order'  || $request['text'] === 'Order View' ||
            $request['text'] === 'Empty trash' || $request['text'] === 'Send') 
            && getCompanyName($request['chat_id']) === '':

            $text = 'Sorry, you need to choose a company first';
            $menu->setMenu([['Choose a company']]);
            break;
        case 'Menu':
            $text = 'What do we do ?';
            $menu->setMenu([['Select products'],['Order View','Send an order'],['Empty trash','Choose a company']]);
            break;
        case 'Select products':
            $text = 'Choose a dish';
            $menu->setMenu(getItemList($request['chat_id']));
            break;
        case ($itm = getItem($request['chat_id'],$request['text'])) != null:
            $text = "Dish added successfully\n" .
                "Name: " . $request['text'] . "\n" .
                "Price " . $itm->price;
            selectItems($request['chat_id'],$request['text'],$itm->id);
            $menu->setMenu([['Menu']]);
            break;
        case 'Order View':
            $text = 'Dishes not selected';
            $items = getSelectItems($request['chat_id']);
            if (!empty($items)) {
                $text = '';
                foreach ($items as $item) {
                    $name = $item['name'] ?? '';
                    $count = $item['count'] ?? '';
                    $company_name = $item['company'] ?? '';
                    $text .= "\n Name: $name, count: $count\n company: $company_name";
                }
            }
            $menu->setMenu([['Menu']]);
            break;
        case 'Empty trash':
            $text = 'Dishes successfully deleted';
            deleteItems($request['chat_id']);
            $menu->setMenu([['Menu']]);
            break;
        case 'Send an order':
            $text = 'Your order is ready to ship, what do we do?';
            $mn [] = ['Send'];
            $mn [] = ['Order View'];
            $items = getSelectItems($request['chat_id']);
            if (!empty($items)) {
                $company = $items[0]['company'];
                foreach ($items as $item) {
                    $company_name = $item['company'] ?? '';
                    if($company_name != $company){
                        $text = 'The order can be sent, but it contains dishes from different companies, perhaps it is worth deleting the order?';
                        $mn [] = ['Empty trash'];
                    }
                }
            }
            $menu->setMenu($mn);
            break;
        case 'Send':
            sendCarts($request['chat_id']);
            deleteItems($request['chat_id']);
            postOrders();
            $text = 'order successfully sent';
            $menu->setMenu([['Menu']]);
            break;
        default:
            $text = 'Unknown command, sorry but I can\'t execute it, you command: ' . $request['text'];
            $menu->setMenu([['Come back']]);
    }

    try {
        sendMessage($bot, $request['chat_id'], $text, $menu->getMenu());
    } catch (TelegramSDKException $e) {
        file_put_contents(ERROR_FILE, $e->getMessage(), FILE_APPEND);
    }
}