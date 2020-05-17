<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EditProfil extends Route
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        return $this->view->render(
            $response,
            'templates/home/editProfil.html.twig',
            [
                'me' => $_SESSION['profil'],
                'characters' => $this->characters,
                'sexualPattern' => $this->sexualPattern,
                'flash' => $this->flash->getMessages(),
                'notification' => $this->notif->getNotification(),
                'year' => date('Y') - 18,
                'editProfil' => true,
            ]
        );
    }

    public function check(Request $request, Response $response, array $args)
    {
        $post = $request->getParsedBody();
        if ($this->form->checkProfil($post)) {
            if ($this->user->updateUser($post)) {
                $_SESSION['profil'] = array_replace($_SESSION['profil'], $post);
                $this->flash->addMessage('success', 'profil updated!');
            } else {
                $this->flash->addMessage('failure', 'something went wrong');
            }
        }
        $this($request, $response, $args);
    }

    public function complete(Request $request, Response $response, array $args)
    {
        $this->flash->addMessage('failure', 'Plz complete your profil before searching for targets');
        $this($request, $response, $args);
    }
}
