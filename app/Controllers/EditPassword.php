<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EditPassword extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        return $this->view->render(
            $response,
            'templates/home/editProfil.html.twig',
            [
                'me' => $_SESSION['profil'],
                'characters' => $this->characters,
                'sexualPattern' => $this->sexualPattern,
                'flash' => $this->flash->getMessages(),
                'year' => date('Y') - 18,
                'notification' => $this->notif->getNotification(),
                'editPwd' => true,
            ]
        );
    }

    public function check(Request $request, Response $response, array $args)
    {
        $post = $request->getParsedBody();
        $this->form->checkPwd($post);
        $this($request, $response, $args);
    }
}
