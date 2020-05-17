<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AddFriendRequest extends Route
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        if (empty($this->friends->getFriendReq($_SESSION['id'], $args['id']))
            && empty($this->friends->getFriend($_SESSION['id'], $args['id']))) {
            $this->friends->setFriendsReq(
                $_SESSION['id'],
                $args['id']
            );
            $flash = 'request sent!';
        } else {
            $flash = 'already sent!';
        }

        return $response->write($flash);
    }
}
