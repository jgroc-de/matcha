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
        $this->client->setAuthConfig(__DIR__ . '/../../code_secret_client_505912914407-r8sntj5k6qcotss7ck33ds7nbj58cotr.apps.googleusercontent.com.json');
    }

    public function login($token): string
    {
        $payload = $this->client->verifyIdToken($token);
        if (empty($payload)) {
            return '';
        }

        return $this->loginOrRegisterUser($payload, $token);
    }
}
