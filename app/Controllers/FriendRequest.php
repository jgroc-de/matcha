<?php

namespace App\Controllers;

use App\Model\FriendsModel;
use App\Model\UserModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FriendRequest
{
    /** @var FriendsModel */
    private $friendsModel;
    /** @var UserModel */
    private $userModel;

    public function __construct(FriendsModel $friendsModel, UserModel $userModel)
    {
        $this->friendsModel = $friendsModel;
        $this->userModel = $userModel;
    }

    public function add(Request $request, Response $response, array $args): Response
    {
        if (!$this->userModel->hasPictures($_SESSION['id'])) {
            $flash = 'You need to add pictures on your profile first!';
        } elseif ($this->isNotAlreadyFriend($_SESSION['id'], $args)) {
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
        if ($this->friendsModel->delFriend(
            $_SESSION['id'],
            $args['id']
        )) {
            $flash = 'request sent!';
        } else {
            $flash = 'already sent!';
        }
        $response->write($flash);

        return $response;
    }

    public function deleteRequest(Request $request, Response $response, array $args): Response
    {
        if ($this->friendsModel->isLiked($_SESSION['id'], $args['id'])) {
            $this->friendsModel->delFriendReq($_SESSION['id'], $args['id']);
            $flash = 'request sent!';
        } else {
            $flash = 'User not liked!';
        }
        $response->write($flash);

        return $response;
    }

    private function isNotAlreadyFriend(int $myId, array $args): bool
    {
        return !$this->friendsModel->isLiked($myId, $args['id'])
            && !$this->friendsModel->isFriend($myId, $args['id']);
    }
}
