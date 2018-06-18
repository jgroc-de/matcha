<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class EditProfil extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            if ($this->form->checkProfil($request))
            {
                $this->user->updateUser();
                $this->flash->addMessage('success', 'profil updated!');
            }
        }
        return $this->view->render(
            $response,
            'templates/home/editProfil.html.twig',
            [
                'profil' => $_SESSION['profil'],
                'characters' => $this->characters,
                'sexualPattern' => $this->sexualPattern,
                'flash' => $this->flash->getMessages(),
                'post' => $_POST
            ]
        );
    }
}
