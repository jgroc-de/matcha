<?php

namespace App\Lib;

use App\Matcha;
use App\Model\UserModel;

abstract class APIinterface
{
    protected $client;
    /** @var UserModel */
    protected $user;
    /** @var FormChecker */
    protected $form;
    /** @var $ft_geoIP */
    protected $ft_geoIP;

    public function __construct($container)
    {
        $this->user = $container->get('user');
        $this->form = $container->get('form');
        $this->ft_geoIP = $container->get('ft_geoIP');
    }

    abstract public function loginToApi($token): string;

    protected function loginOrRegisterUser($payload, $id_token): string
    {
        $user = $this->user->getAuthUserByEmail($payload['email']);
        if (empty($user)) {
            $pseudo = $payload['given_name'] . rand(0, 10000000);
            while ($this->user->getUserByEmail($pseudo)) {
                $pseudo = $payload['given_name'] . rand(0, 10000000);
            }
            $gender = Matcha::GENDER[rand(0, 4)];
            $user = [
                'gender' => $gender,
                'pseudo' => $pseudo,
                'email' => $payload['email'],
                'name' => $payload['given_name'],
                'surname' => $payload['family_name'],
                'birthdate' => 2000,
                'sexuality' => Matcha::KIND[0],
                'biography' => '',
                'password' => '',
                'activ' => 1,
                'token' => $id_token,
                'bot' => 'false',
                'lat' => rand(485500, 490500) / 10000,
                'lng' => rand(21000, 26000) / 10000,
                'popularity' => 0,
                'lastlog' => time(),
                'publicToken' => 'oauth',
                'img' => $payload['picture'],
            ];
            $this->user->setUser($user);
            $user = $this->user->getUser($user['pseudo']);
            $this->user->setOauth($user['id'], true);
            $this->ft_geoIP->setLatLng($user);
            $urlRedirect = '/editProfil';
        } else {
            $urlRedirect = '/';
        }
        $this->form->setSession($user);

        return $urlRedirect;
    }
}
