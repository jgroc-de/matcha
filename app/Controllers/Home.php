<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Home extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $template = 'templates/home/profil.html.twig';
        $twigVar = [
            'profil' => $_SESSION['profil'],
            'me' => $_SESSION['profil'],
            'friendReq' => $this->friends->getFriendsReqs($_SESSION['id']),
            'friends' => $this->friends->getFriends($_SESSION['id']),
            'tags' => $this->tag->getUserTags($_SESSION['id']),
            'notification' => $this->notif->getNotification(),
        ];

        return $this->view->render($response, $template, $twigVar);
    }
}
