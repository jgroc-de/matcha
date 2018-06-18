<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Profil extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $user = $this->user->getUserById($args['id']);
        if ($user)
        {
            return $this->view->render(
                $response,
                'templates/home/profil.html.twig',
                [
                    'profil' => $user,
                    'me' => $_SESSION['profil'],
                    'tags' => $this->tag->getUserTags($user['id'])
                ]
            );
        }
    }
}
