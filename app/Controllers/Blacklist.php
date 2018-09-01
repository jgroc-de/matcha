<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Blacklist extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $this->container->friends->delFriend($args['id'], $_SESSION['id']);
        if (!$this->container->blacklist->getBlacklistById($_SESSION['id'], $args['id']))
        {
            $this->container->blacklist->setBlacklist($args['id']);
            $flash = 'This user is now on your blacklist!';
        }
        else
            $flash = 'This user is already on your blacklist!';
        return $response->getBody()->write($flash);
    }
}
