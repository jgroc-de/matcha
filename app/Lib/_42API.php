<?php

namespace App\Lib;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;

class _42API extends APIAbstract
{
    /** @var Client */
    protected $client;

    private $baseUrl;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->client = $container->get('curl');
        $this->baseUrl = $container->get('settings')['siteUrl'];
    }

    public function loginToApi($token): string
    {
        try {
            $curlResponse = $this->client->post(
                'https://api.intra.42.fr/oauth/token',
                [
                    'form_params' => [
                        'grant_type' => 'authorization_code',
                        'client_id' => $_ENV['PUB_42_KEY'],
                        'client_secret' => $_ENV['SECRET_42_KEY'],
                        'code' => $token,
                        'redirect_uri' => $this->baseUrl . '/apiLogin/42',
                    ],
                ]
            );
            $json = json_decode($curlResponse->getBody());
            $curlRequest = new Request('GET', 'https://api.intra.42.fr/v2/me', [
                'Authorization' => 'Bearer ' . $json->access_token,
            ]);
            $curlResponse = $this->client->send($curlRequest);
            $json = json_decode($curlResponse->getBody());
        } catch (ClientException $error) {
            //print($error->getMessage());
            return '/';
        } catch (GuzzleException $error) {
            return '/';
        }
        $payload = [
            'email' => $json->email,
            'given_name' => $json->login,
            'family_name' => $json->last_name,
            'picture' => $json->image_url,
        ];

        return $this->loginOrRegisterUser($payload, $token);
    }
}
