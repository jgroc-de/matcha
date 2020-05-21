<?php

namespace App\Controllers;

use App\Lib\MailSender;
use App\Model\BlacklistModel;
use App\Model\FriendsModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Blacklist
{
    /** @var FriendsModel */
    private $friends;
    /** @var BlacklistModel */
    private $blacklist;
    /** @var MailSender */
    private $mail;

    public function __construct(
        FriendsModel $friendsModel,
        BlacklistModel $blacklistModel,
        MailSender $mailSender
    ) {
        $this->friends = $friendsModel;
        $this->blacklist = $blacklistModel;
        $this->mail = $mailSender;
    }

    public function addToBlacklist(Request $request, Response $response, array $args): Response
    {
        $flash = $this->deleteFriendAndBlacklist($args['id']);
        $response->getBody()->write($flash);

        return $response;
    }

    public function report(Request $request, Response $response, array $args): Response
    {
        $this->deleteFriendAndBlacklist($args['id']);
        $this->mail->reportMail($args['id']);
        $response->getBody()->write('Thank you to help us improved the community!');

        return $response;
    }

    private function deleteFriendAndBlacklist(int $id): string
    {
        $this->friends->delFriend($id, $_SESSION['id']);
        if (!$this->blacklist->getBlacklistById($id, $_SESSION['id'])) {
            $this->blacklist->setBlacklist($id);

            return 'This user is now on your blacklist!';
        }

        return 'This user is already on your blacklist!';
    }
}
