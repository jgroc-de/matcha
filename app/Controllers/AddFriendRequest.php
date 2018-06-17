<?php

namespace App;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class AddFriendRequest extends \App\Constructor
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
        $response->getBody()->write(
            $this->container->friends->setFriendsReq(
                $_SESSION['id'],
                $args['id']
            ));
        return $response;
    }
}
