<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class AddFriendRequest extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $response->getBody()->write(
            $this->container->friends->setFriendsReq(
                $_SESSION['id'],
                $args['id']
            ));
        return $response;
    }
}
