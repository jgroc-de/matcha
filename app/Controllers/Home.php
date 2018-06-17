<?php

namespace App;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Home extends \App\Constructor
{
    public function route(Request $request, Response $response, array $args)
    {
        return $this->view->render(
            $response,
            'templates/home/profil.html.twig',
            [
                'profil' => $_SESSION['profil'],
                'me' => $_SESSION['profil'],
                'friendReq' => $this->friends->getFriendsReqs($_SESSION['id']),
                'friends' => $this->friends->getFriends($_SESSION['id']),
                'tags' => $this->tag->getUserTags($_SESSION['id'])
            ]
        );
    }
}
