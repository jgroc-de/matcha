<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DeleteUserTag extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        if ($this->container->tag->delUserTag($args['id'], $_SESSION['id'])) {
            return $response;
        }

        return $response->withStatus(400);
    }
}
