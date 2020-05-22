<?php

namespace App\Lib;

use Google_Client;

class googleAPI extends APIinterface
{
    /** @var Google_Client */
    protected $client;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->client = new Google_Client();
        $this->client->setAuthConfig(__DIR__ . '/../../ggApi.json');
    }

    public function loginToApi($token): string
    {
        $payload = $this->client->verifyIdToken($token);
        if (empty($payload)) {
            return '';
        }

        return $this->loginOrRegisterUser($payload, $token);
    }
}
