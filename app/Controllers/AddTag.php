<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class AddTag extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        if ($this->container->tag->setUserTag($_POST['tag']))
        {
            $tag = $this->tag->getUserTagByName($_POST['tag'], $_SESSION['id']);
            return $response->write($tag['id']);
        }
        return $response->withStatus(400);
    }
}
