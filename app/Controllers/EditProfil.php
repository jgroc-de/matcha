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
            $post['publicToken'] = $_SESSION['profil']['publicToken'];
        }
        else
            $post = $_SESSION['profil'];
        if ($request->getUri()->getPath() == '/completeProfil')
                $this->flash->addMessage('failure', 'Plz complete your profil before searching for targets');
        return $this->view->render(
            $response,
            'templates/home/editProfil.html.twig',
            [
                'me' => $post,
                'characters' => $this->characters,
                'sexualPattern' => $this->sexualPattern,
                'flash' => $this->flash->getMessages(),
                'notification' => $this->notif->getNotification(),
                'year' => date('Y') - 18,
                'editProfil' => true
            ]
        );
    }
}
