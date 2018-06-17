<?php

namespace App;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class AddTag extends \App\Constructor
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
        if ($this->container->tag->setUserTag($_POST['tag']))
        {
            $tag = $this->tag->getUserTagByName($_POST['tag'], $_SESSION['id']);
            $response->getBody()->write($tag['id']);
            return $response;
        }
        return $response->withStatus(400);
    }
}
