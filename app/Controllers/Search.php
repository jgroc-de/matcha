<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Search extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        return $this->view->render(
            $response,
            'templates/home/search.html.twig',
            [
                'me' => $_SESSION['profil'],
                'users' => $this->user->getUsers()
            ]
        );
    }
}
