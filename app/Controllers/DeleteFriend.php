<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DeleteFriend extends Route
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->friends->delFriend(
            $_SESSION['id'],
            $args['id']
        );

        return $response;
    }
}
