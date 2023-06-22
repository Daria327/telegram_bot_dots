<?php

namespace api;

require_once ( __DIR__ . '/../config.php' );
require_once ( __DIR__ . '/../vendor/autoload.php' );

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;

class DotsApi
{
    private Client $client;

    public function __construct() {
        $this->client = new Client();
    }

    /**
     * @throws GuzzleException
     */
    public function getRequest(string $url) : Response{
        return $this->client->get(
            'https://' . $url,
            [
                'headers' => [
                    'Api-Token' => API_TOKEN,
                    'Api-Account-Token' => ACCOUNT_TOKEN,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'v'=> '2.0.0',
                ],
            ]
        );
    }

    /**
     * @throws GuzzleException
     */
    public function postCart( string $url, string $str, array $arr ) : void {
        $this->client->post(
            'https://'.$url,
            [
                'headers' => [
                    'Api-Token' => API_TOKEN,
                    'Api-Account-Token' => ACCOUNT_TOKEN,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'v'=> '2.0.0',
                ],
                'json' => [
                    'companyId' => $str,
                    'cartItems' => [
                        $arr
                    ],
                ],
            ]
        );
    }

    public function postOrder() : void {
        $this->client->post(
            'https://' . 'clients-api.dots.live/api/v2/orders',
            [
                'headers' => [
                    'Api-Auth-Token' => AUTH_TOKEN,
                    'Api-Token' => API_TOKEN,
                    'Api-Account-Token' => ACCOUNT_TOKEN,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'v'=> '2.0.0',
                ],
            ]
        );
    }
}