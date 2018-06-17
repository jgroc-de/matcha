<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class AddTag extends \App\Constructor
{
    public function route(Request $request, Response $response, array $args)
    {
        if ($this->container->tag->setUserTag($_POST['tag']))
        {
            $tag = $this->tag->getUserTagByName($_POST['tag'], $_SESSION['id']);
            $response->getBody()->write($tag['id']);
            return $response;
        }
        return $response->withStatus(400);
    }
}
