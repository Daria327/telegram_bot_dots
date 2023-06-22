<?php

namespace bot;

require_once ( __DIR__ . '/../vendor/autoload.php' );

use Telegram\Bot\Keyboard\Keyboard;


class Menu
{
    private array $menu;

    public function setMenu(array $mn) : void {
        $this->menu = $mn;
    }

    public function getMenu() : Keyboard {
        return Keyboard::make([
            'keyboard' => $this->menu,
            'resize_keyboard' => true,
            'one_time_keyboard' => false
        ]);
    }

}