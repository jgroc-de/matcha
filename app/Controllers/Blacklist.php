<?php

namespace App\Controllers;

use App\Lib\MailSender;
use App\Model\BlacklistModel;
use App\Model\FriendsModel;
use App\Model\UserModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Blacklist
{
    /** @var BlacklistModel */
    private $blacklist;
    /** @var FriendsModel */
    private $friends;
    /** @var UserModel */
    private $user;
    /** @var MailSender */
    private $mail;

    public function __construct(
        BlacklistModel $blacklistModel,
        FriendsModel $friendsModel,
        UserModel $userModel,
        MailSender $mailSender
    ) {
        $this->blacklist = $blacklistModel;
        $this->friends = $friendsModel;
        $this->user = $userModel;
        $this->mail = $mailSender;
    }

    public function add(Request $request, Response $response, array $args): Response
    {
        if ($this->deleteFriendAndBlacklist($args['id'])) {
            $flash = 'This user is now on your blacklist!';
        } else {
            $flash = 'This user is already on your blacklist!';
        }

        return $response->withJson([FlashMessage::SUCCESS => $flash]);
    }

    public function report(Request $request, Response $response, array $args): Response
    {
        if ($this->deleteFriendAndBlacklist($args['id'])) {
            $this->mail->reportMail($args['id']);
            $flash = 'Thank you to help us improved the community!';
        } else {
            $flash = 'Already reported';
        }

        return $response->withJson([FlashMessage::SUCCESS => $flash]);
    }

    private function deleteFriendAndBlacklist(int $id): bool
    {
        if (!$this->user->hasUser($id)) {
            return false;
        }
        $this->friends->delFriend($id, $_SESSION['id']);
        $this->friends->delFriendReq($id, $_SESSION['id']);

        return $this->blacklist->setBlacklist($id);
    }
}
