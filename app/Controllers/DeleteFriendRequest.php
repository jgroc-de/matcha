<?php

namespace App;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class DeleteFriend extends \App\Constructor
{
    public function route(Request $request, Response $response, array $args)
    {
        $this->container->friends->delFriendReq(
            $_SESSION['id'],
            $args['id']
        );
        return $response;
    }
}
