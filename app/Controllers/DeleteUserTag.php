<?php

namespace App;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class DeleteUserTag extends \App\Constructor
{
    public function route(Request $request, Response $response, array $args)
    {
        if ($this->container->tag->delUserTag($args['id'], $_SESSION['id']))
            return $response;
        else
            return $response->withStatus(400);
    }
}
