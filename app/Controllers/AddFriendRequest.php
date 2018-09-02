<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class AddFriendRequest extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        if (!($this->friends->getFriendReq($args['id'], $_SESSION['id'])))
        {
            $this->container->friends->setFriendsReq(
                $_SESSION['id'],
                $args['id']
            );
            $flash = 'request sent!';
        }
        else
        {
            $flash = 'already sent!';
        }
        return $response->write($flash);
    }
}
