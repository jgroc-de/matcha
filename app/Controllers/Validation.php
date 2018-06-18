<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Validation extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $get = $request->getParams();
        $account = $this->user->getUser($get['login']);
        if ($account && ($get['token'] === $account['token']))
        {
            $_SESSION['pseudo'] = $account['pseudo'];
            $_SESSION['id'] = $account['id'];
            $_SESSION['profil'] = $account;
            if ($get['action'] === 'reinit')
                return $response->withRedirect('/password');
            $this->user->activate();
        }
        return $response->withRedirect('/');
    }
}
