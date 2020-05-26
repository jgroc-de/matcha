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

        if (!file_exists(__DIR__ . '/../../ggApi.json')) {
            $conf = json_decode($_ENV['GG_CONF'], true);
            $this->client = new Google_Client($conf['web']);
        } else {
            $this->client = new Google_Client();
            $this->client->setAuthConfig(__DIR__ . '/../../ggApi.json');
        }
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
