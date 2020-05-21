<?php

namespace App\Controllers;

use App\Model\FriendsModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FriendRequest
{
    /** @var FriendsModel */
    private $friendsModel;

    public function __construct(FriendsModel $friendsModel)
    {
        $this->friendsModel = $friendsModel;
    }

    public function add(Request $request, Response $response, array $args): Response
    {
        if (empty($this->friendsModel->getFriendReq($_SESSION['id'], $args['id']))
            && empty($this->friendsModel->getFriend($_SESSION['id'], $args['id']))) {
            $this->friendsModel->setFriendsReq(
                $_SESSION['id'],
                $args['id']
            );
            $flash = 'request sent!';
        } else {
            $flash = 'already sent!';
        }
        $response->write($flash);

        return $response;
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $this->friendsModel->delFriend(
            $_SESSION['id'],
            $args['id']
        );

        return $response;
    }

    public function deleteRequest(Request $request, Response $response, array $args): Response
    {
        $this->friendsModel->delFriendReq(
            $_SESSION['id'],
            $args['id']
        );

        return $response;
    }
}
