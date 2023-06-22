<?php
require_once ( __DIR__ . '/api/query.php' );
require_once ( __DIR__ . '/vendor/autoload.php' );
require_once ( __DIR__ . '/config.php' );
require_once ( __DIR__ . '/databases/query.php' );

use GuzzleHttp\Exception\GuzzleException;

function getCitiesList() : array {
    try {
        return getList(getCities());
    } catch (GuzzleException $e) {
        file_put_contents(ERROR_FILE, $e->getMessage(), FILE_APPEND);
    }
    return [];
}

function citySearch( string $text ) : bool {
    foreach (getCitiesList() as $city){
        if ($city[0] === $text){
            return true;
        }
    }
    return false;
}

function getCityID( string $name ) : string {
    try {
        foreach (getCities() as $city){
            if($city->name == $name){
                return $city->id;
            }
        }
    } catch (GuzzleException $e) {
        file_put_contents(ERROR_FILE, $e->getMessage(), FILE_APPEND);
    }
}

function getCompanyList( string $city ) : array {
    try {
        return getList(getCompany(getCityID($city)));
    } catch (GuzzleException $e) {
        file_put_contents(ERROR_FILE, $e->getMessage(), FILE_APPEND);
    }
    return [];
}

function companySearch( string $text, string $chat_id )  : bool {
    foreach (getCompanyList(getCityName($chat_id)) as $company){
        if ($company[0] == $text){
            return true;
        }
    }
    return false;
}

function getCompanyID( string $chat_id, string $name ) : string {
    try {
        $city_id = getCityID(getCityName($chat_id));
        foreach (getCompany($city_id) as $company){
            if($company->name === $name){
                return $company->id;
            }
        }
    } catch (GuzzleException $e) {
        file_put_contents(ERROR_FILE, $e->getMessage(), FILE_APPEND);
    }
}

function getItemList( string $chat_id ) : array {
    $items_name = [];
    try {
        foreach (getItems(getCompanyID($chat_id,getCompanyName($chat_id))) as $items){
            $items_name [] = [$items->items[0]->name];
        }
    } catch (GuzzleException $e) {
        file_put_contents(ERROR_FILE, $e->getMessage(), FILE_APPEND);
    }
    return $items_name;
}

function getItem( string $chat_id, string $name ) : ?stdClass {
    try {
        foreach (getItems(getCompanyID($chat_id,getCompanyName($chat_id))) as $items){
            if ($items->items[0]->name === $name)
                return $items->items[0];
        }
    } catch (GuzzleException $e) {
        file_put_contents(ERROR_FILE, $e->getMessage(), FILE_APPEND);
    }
    return null;
}

function getCompanyIDByName( string $name, string $city ) : string {
    try {
        $city_id = getCityID($city);
        foreach (getCompany($city_id) as $company){
            if($company->name === $name){
                return $company->id;
            }
        }
    } catch (GuzzleException $e) {
        file_put_contents(ERROR_FILE, $e->getMessage(), FILE_APPEND);
    }
}

function getList( array $items ) : array {
    $items_name = [];
    foreach ($items as $item) {
        $items_name[] = [$item->name];
    }
    return $items_name;
}

function sendCarts( string $chat_id ) : void {
    $arr = getSelectItems($chat_id);
    foreach ($arr as $el){
        $array = ["id" => $el['itemId'], "count" => (int)$el['count']];
        try {
            postCarts(getCompanyIDByName($el['company'], $el['city']), $array);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            file_put_contents(ERROR_FILE, $e->getMessage(), FILE_APPEND);
        }

    }
}