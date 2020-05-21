<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RGPD
{
    private $container;

    public function __construct(
        $container
    ) {
        $this->container = $container;
    }

    public function __get($name)
    {
        return $this->container->get($name);
    }

    public function getAllDatas(Request $request, Response $response, array $args): Response
    {
        $this->common->sendAllDatas();
        $response->getBody()->write('Check your mailbox!');

        return $response;
    }

    public function validationDeletion(Request $request, Response $response, array $args): Response
    {
        $get = $request->getParams();
        if (!$this->validator->validate($get, ['id', 'token', 'action'])) {
            return $response->withStatus(400);
        }
        $account = $this->user->getUserById($get['id']);
        if (empty($account) || ($get['token'] !== $account['token'])) {
            return $response->withRedirect('/');
        }
        $_SESSION['id'] = $account['id'];
        $_SESSION['profil'] = $account;
        $_SESSION['profil']['token'] = password_hash(random_bytes(6), PASSWORD_DEFAULT);
        $this->user->updateToken($account['pseudo'], $_SESSION['profil']['token']);
        if ($get['action'] === 'ini') {
            return $response->withRedirect('/editPassword');
        }
        if ($get['action'] === 'del') {
            $this->common->deleteAccountExecute();

            return $response->withRedirect('/logout');
        }
        $this->user->activate();

        return $response->withRedirect('/');
    }
}
