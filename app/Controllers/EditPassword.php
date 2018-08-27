<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class EditPassword extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->form->check($request))
        {
            if ($_POST['password'] === $_POST['password1'])
            {
                $this->user->updatePassUser();
                $this->flash->addMessage('success', 'password updated!');
            }
            else
                $this->flash->addMessage('fail', 'passwords doesnt match');
        }
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
                'editPwd' => true
            ]
        );
    }
}
