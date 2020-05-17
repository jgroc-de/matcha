<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DeleteFriendRequest extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $this->container->friends->delFriendReq(
            $_SESSION['id'],
            $args['id']
        );

        return $response;
    }
}
