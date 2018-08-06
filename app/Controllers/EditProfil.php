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
            if ($post = $this->form->checkProfil($request))
            {
                if ($this->user->updateUser($post))
                {
                    $_SESSION['profil'] = array_replace($_SESSION['profil'], $post);
                    $this->flash->addMessage('success', 'profil updated!');
                }
                else
                    $this->flash->addMessage('failure', 'something went wrong');
            }
        }
        else
            $post = $_SESSION['profil'];
        return $this->view->render(
            $response,
            'templates/home/editProfil.html.twig',
            [
                'profil' => $post,
                'characters' => $this->characters,
                'sexualPattern' => $this->sexualPattern,
                'flash' => $this->flash->getMessages(),
                'editProfil' => true
            ]
        );
    }
}
