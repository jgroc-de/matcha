<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class AjaxController extends \App\Constructor
{
    public function friendRequest (Request $request, Response $response, array $args)
    {
        $response->getBody()->write(
            $this->container->friends->setFriendsReq(
                $_SESSION['id'], $args['id']
            ));
        return $response;
    }
    
    public function chat (Request $request, Response $response, array $args)
    {
        $friends = $this->container->friends;
        if ($friends->getFriend($_SESSION['id'], $args['id']))
        {
            $response->getBody()->write('yep');
        }
        else if ($friends->getFriendReq($_SESSION['id'], $args['id']) && $friends->getFriendReq($args['id'], $_SESSION['id']))
        {
            $friends->setFriend($_SESSION['id'], $args['id']);
            $this->chat($request, $response, $args);
        }
        else
            $response->getBody()->write('nop');
        return $response;
    }
}
