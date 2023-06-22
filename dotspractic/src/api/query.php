<?php
require_once ( __DIR__ . '/DotsApi.php' );
require_once ( __DIR__ . '/../vendor/autoload.php' );
require_once ( __DIR__ . '/../config.php' );

use api\DotsApi;
use GuzzleHttp\Exception\GuzzleException;

$dots = new DotsApi();

/**
 * @throws GuzzleException
 */
function getCities() : array {
    return getApi('clients-api.dots.live/api/v2/cities');
}

/**
 * @throws GuzzleException
 */
function getCompany( string $id ) : array {
    return getApi("clients-api.dots.live/api/v2/cities/$id/companies");
}

/**
 * @throws GuzzleException
 */
function getItems(string $id ) : array {
    return getApi("clients-api.dots.live/api/v2/companies/$id/items-by-categories");
}

/**
 * @throws GuzzleException
 */
function getApi( string $url ) : array {
    global $dots;
    $body = ($dots->getRequest($url))->getBody();
    return json_decode((string) $body)->items;
}

/**
 * @throws GuzzleException
 */
function postCarts( string $companyID, array $arr ) : void {
    global $dots;
    $url = 'clients-api.dots.live/api/v2/cart/info';
    $dots->postCart($url, $companyID, $arr);
}

function postOrders() : void {
    global $dots;
    $dots->postOrder();
}