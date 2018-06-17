<?php

namespace App;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Tchat extends \App\Constructor
{
    /**
     * @param $request RequestInterface
     * @param $response ResponseInterface
     * @param $args array
     *
     * @return $response ResponseInterface
     */
    public function route(Request $request, Response $response, array $args)
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
