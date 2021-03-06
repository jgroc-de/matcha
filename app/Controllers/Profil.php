<?php

namespace App\Controllers;

use App\Lib\CustomError;
use App\Lib\MyZmq;
use App\Model\BlacklistModel;
use App\Model\FriendsModel;
use App\Model\NotificationModel;
use App\Model\TagModel;
use App\Model\UserModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class Profil
{
    const template = 'templates/in/profil.html.twig';

    /** @var BlacklistModel */
    private $blacklist;
    /** @var FriendsModel */
    private $friends;
    /** @var MyZmq */
    private $MyZmq;
    /** @var NotificationModel */
    private $notif;
    /** @var CustomError */
    private $notFoundHandler;
    /** @var TagModel */
    private $tag;
    /** @var Twig */
    private $view;
    /** @var UserModel */
    private $user;

    public function __construct(
        BlacklistModel $blacklistModel,
        CustomError $customError,
        FriendsModel $friendsModel,
        MyZmq $myZmq,
        NotificationModel $notificationModel,
        TagModel $tagModel,
        Twig $view,
        UserModel $userModel
    ) {
        $this->blacklist = $blacklistModel;
        $this->notFoundHandler = $customError;
        $this->view = $view;
        $this->user = $userModel;
        $this->tag = $tagModel;
        $this->friends = $friendsModel;
        $this->MyZmq = $myZmq;
        $this->notif = $notificationModel;
    }

    public function page(Request $request, Response $response, array $args): Response
    {
        return $this->view->render(
            $response,
            self::template,
            [
                'profil' => $_SESSION['profil'],
                'me' => $_SESSION['profil'],
                'imgs' => $this->getImgs($_SESSION['profil']),
                'friendReq' => $this->friends->getFriendsReqs($_SESSION['id']),
                'friends' => $this->friends->getFriends($_SESSION['id']),
                'tags' => $this->tag->getUserTags($_SESSION['id']),
                'notification' => $this->notif->getNotification(),
                'mapKey' => $_ENV['GMAP_KEY'],
                'trendingTags' => $this->tag->getMostUsedTags()
            ]
        );
    }

    public function profil(Request $request, Response $response, array $args): Response
    {
        if ($args['id'] === $_SESSION['id']) {
            return $response->withRedirect('/', 302);
        }
        $user = $this->user->getUserById($args['id']);
        if (!$this->blacklist->isBlacklistById($args['id'], $_SESSION['id']) && !empty($user)) {
            $this->MyZmq->send([
                'category' => '"' . $user['publicToken'] . '"',
                'dest' => $user['id'],
                'exp' => $_SESSION['id'],
                'link' => '/profil/' . $_SESSION['id'],
                'msg' => $_SESSION['profil']['pseudo'] . ' watched your profil!',
            ]);
            $isFriend = $this->friends->isFriend($_SESSION['id'], $user['id']);
            $isLiked = $this->friends->isLiked($_SESSION['id'], $user['id']);
            $user['pseudo_decorated'] = $user['pseudo'];
            if ($isFriend) {
                $user['pseudo_decorated'] .= ' "Friend"';
            } elseif ($isLiked) {
                $user['pseudo_decorated'] .= ' "Liked"';
            }

            return $this->view->render(
                $response,
                self::template,
                [
                    'isFriend' => $isFriend,
                    'isLiked' => $isLiked,
                    'profil' => $user,
                    'imgs' => $this->getImgs($user),
                    'me' => $_SESSION['profil'],
                    'tags' => $this->tag->getUserTags($user['id']),
                    'notification' => $this->notif->getNotification(),
                    'mapKey' => $_ENV['GMAP_KEY'],
                ]
            );
        }

        return ($this->notFoundHandler)($request, $response);
    }

    private function getImgs(array $user): array
    {
        $imgs = [
            'imgs' => [],
            'pseudo' => $user['pseudo'],
        ];
        foreach ($user as $key => $value) {
            if (strpos($key, 'img') === 0) {
                $imgs['imgs'][] = $value;
            }
        }

        return $imgs;
    }
}
