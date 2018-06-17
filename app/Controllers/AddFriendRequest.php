<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class AddFriendRequest extends \App\Constructor
{
    public function route(Request $request, Response $response, array $args)
    {
        $response->getBody()->write(
            $this->container->friends->setFriendsReq(
                $_SESSION['id'],
                $args['id']
            ));
        return $response;
    }
}
