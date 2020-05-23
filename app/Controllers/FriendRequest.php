<?php

namespace App\Controllers;

use App\Lib\MyZmq;
use App\Model\BlacklistModel;
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
    /** @var MyZmq */
    private $myZmq;
    /** @var BlacklistModel */
    private $blacklist;

    public function __construct(
        BlacklistModel $blacklistModel,
        FriendsModel $friendsModel,
        UserModel $userModel,
        MyZmq $myZmq
    ) {
        $this->blacklist = $blacklistModel;
        $this->friendsModel = $friendsModel;
        $this->userModel = $userModel;
        $this->myZmq = $myZmq;
    }

    public function add(Request $request, Response $response, array $args): Response
    {
        if (!$this->userModel->hasPictures($_SESSION['id'])) {
            $flash = 'You need to add pictures on your profile first!';
        } elseif ($this->isNotAlreadyFriendOrBlacklisted($_SESSION['id'], $args['id'])) {
            $user = $this->userModel->getUserById($args['id']);
            if (empty($user)) {
                return $response->withRedirect(404);
            }
            if ($this-> friendsModel->isLiked($args['id'], $_SESSION['id']) || $user['bot']) {
                $this->friendsModel->setFriend($_SESSION['id'], $args['id']);
                $flash = 'You have a new Friend! Congrats!';
                $this->userModel->updatePopularity(5, $user);
                $this->userModel->updatePopularity(5, $_SESSION['profil']);
                $this->sendNotif(
                    $user['publicToken'],
                    $user['id'],
                    $_SESSION['id'],
                    $user['id'],
                    "It's a match! say hi to " . $_SESSION['profil']['pseudo']
                );
                $this->sendNotif(
                    $_SESSION['profil']['publicToken'],
                    $_SESSION['id'],
                    $user['id'],
                    $_SESSION['id'],
                    "It's a match! say hi to " . $user['pseudo']
                );
            } else {
                $this->friendsModel->setFriendsReq($_SESSION['id'], $args['id'], $user);
                $this->userModel->updatePopularity(1, $user);
                $this->sendNotif(
                    $user['publicToken'],
                    $user['id'], $_SESSION['id'],
                    $_SESSION['id'],
                    $_SESSION['profil']['pseudo'] . ' sent you a friend request'
                );
                $flash = 'Request Sent!';
            }
        } else {
            $flash = 'already sent!';
        }
        $response->write($flash);

        return $response;
    }

    private function sendNotif($token, $destID, $expID, $friendId, $msg)
    {
        $notif = [
            'category' => '"' . $token . '"',
            'dest' => $destID,
            'exp' => $expID,
            'link' => '/profil/' . $friendId,
            'msg' => $msg,
        ];
        $this->myZmq->send($notif);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        if ($this->friendsModel->delFriend(
            $_SESSION['id'],
            $args['id']
        )) {
            $user = $this->userModel->getUserById($args['id']);
            $msg = [
                'category' => '"' . $user['publicToken'] . '"',
                'dest' => $user['id'],
                'exp' => $_SESSION['id'],
                'link' => '/',
                'msg' => $_SESSION['profil']['pseudo'] . ' has erased your friendship link',
            ];
            $this->myZmq->send($msg);
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

    private function isNotAlreadyFriendOrBlacklisted(int $myId, int $targetId): bool
    {
        return !$this->friendsModel->isLiked($myId, $targetId)
            && !$this->friendsModel->isFriend($myId, $targetId)
            && !$this->blacklist->isBlacklistById($myId, $targetId);
    }
}
