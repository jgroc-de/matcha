<?php

namespace App\Controllers;

use App\Lib\MyZmq;
use App\Lib\Validator;
use App\Model\BlacklistModel;
use App\Model\FriendsModel;
use App\Model\MessageModel;
use App\Model\NotificationModel;
use App\Model\UserModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class Chat
{
    const CHAT_MSG = [
        'Jeeeezz… another dumbass pervert?',
        'U wanna my dickpick?',
        'Hi dickhead',
        'Hi sweetheart',
        'Stop stalking around, go watch TV!',
        'You fight like a Dairy Farmer!',
        'This is the END for you, you gutter crawling cur!',
        "I've spoken with apes more polite than you!",
        "Soon you'll be wearing my sword like a shish kebab!",
        'People fall at my feet when they see me coming!',
        "I'm not going to take your insolence sitting down!",
        'I once owned a dog that was smarter than you.',
        "Nobody's ever drawn blood from me and nobody ever will.",
        'Have you stopped wearing diapers yet?',
        'There are no words for how disgusting you are.',
        'You make me want to puke.',
        'My handkerchief will wipe up your blood!',
        'I got this scar on my face during a mighty struggle!',
        "I've heard you are a contemptible sneak.",
        "You're no match for my brains, you poor fool.",
        'You have the manners of a beggar. ',
    ];

    /** @var BlacklistModel */
    private $blacklist;
    /** @var FriendsModel */
    private $friends;
    /** @var MessageModel */
    private $msg;
    /** @var MyZmq */
    private $myZmq;
    /** @var NotificationModel */
    private $notif;
    /** @var UserModel */
    private $user;
    /** @var Twig */
    private $view;
    /** @var Validator */
    private $validator;

    public function __construct(
        BlacklistModel $blacklistModel,
        FriendsModel $friendsModel,
        MessageModel $messageModel,
        MyZmq $myZmq,
        NotificationModel $notificationModel,
        UserModel $userModel,
        Validator $validator,
        Twig $view
    ) {
        $this->blacklist = $blacklistModel;
        $this->friends = $friendsModel;
        $this->msg = $messageModel;
        $this->myZmq = $myZmq;
        $this->notif = $notificationModel;
        $this->user = $userModel;
        $this->validator = $validator;
        $this->view = $view;
    }

    public function page(Request $request, Response $response, array $args): Response
    {
        $friends = $this->friends->getFriends($_SESSION['id']);
        if (empty($friends)) {
            return $response->withRedirect('/search');
        }

        return $this->view->render(
            $response,
            'templates/in/chat.html.twig',
            [
                'me' => $_SESSION['profil'],
                'notification' => $this->notif->getNotification(),
                'friends' => $friends,
            ]
        );
    }

    public function send(Request $request, Response $response, array $args): Response
    {
        $post = $request->getParsedBody();
        $keys = ['id', 'token', 'msg'];
        if ($this->validator->validate($post, $keys)) {
            $tab = [$_SESSION['id'], $post['id']];
            sort($tab);
            if ($this->friends->isFriend($_SESSION['id'], $post['id'])) {
                $msg = [
                    'category' => $post['token'],
                    'exp' => $_SESSION['id'],
                    'dest' => $post['id'],
                    'msg' => htmlentities($post['msg']),
                    'myId' => $_SESSION['id'],
                    'when' => time(),
                ];
                $this->msg->setMessage([$tab[0], $tab[1], $_SESSION['id'], $msg['msg'], date('Y-m-d H:i:s')]);
                $this->myZmq->send($msg);
                if ($this->user->isBot($post['id'])) {
                    $msg = [
                        'category' => $post['token'],
                        'exp' => $post['id'],
                        'dest' => $_SESSION['id'],
                        'msg' => self::CHAT_MSG[rand(0, 20)],
                        'myId' => $post['id'],
                        'when' => time(),
                    ];
                    $this->myZmq->send($msg);
                    $this->msg->setMessage([$tab[0], $tab[1], $post['id'], $msg['msg'], date('Y-m-d H:i:s')]);
                }

                return $response->withstatus(200);
            }

            return $response->withstatus(403);
        }

        return $response->withstatus(404);
    }

    public function startChat(Request $request, Response $response, array $args): Response
    {
        $id = intval($args['id']);
        $tab = [$_SESSION['id'], $id];
        sort($tab);
        if ($this->friends->isFriend($_SESSION['id'], $id)) {
            $user = $this->user->getUserById($id);
            $msg = [
                'category' => '"' . $user['publicToken'] . '"',
                'dest' => $id,
                'exp' => $_SESSION['id'],
                'msg' => $_SESSION['profil']['pseudo'] . ' is starting a chat session!',
                'link' => '/tchat',
            ];
            $this->myZmq->send($msg);
            $msgs = $this->msg->getMessages($tab);

            return $response->withJson($msgs);
        }

        return $response->withstatus(403);
    }

    public function profilStatus($request, $response, $args): Response
    {
        if (!$this->blacklist->isBlacklistById($args['id'], $_SESSION['id'])) {
            $user = $this->user->getUserById($args['id']);
            $msg = [
                'category' => '"' . $_SESSION['profil']['publicToken'] . '"',
                'profilStatus' => '"' . $user['publicToken'] . '"',
            ];
            $this->myZmq->send($msg);

            return $response;
        }

        return $response->withStatus(404);
    }

    public function mateStatus(Request $request, Response $response, array $args): Response
    {
        $friends = $this->friends->getFriends($_SESSION['id']);
        $msg = [
            'category' => '"' . $_SESSION['profil']['publicToken'] . '"',
            'mateStatus' => [],
        ];
        foreach ($friends as $friend) {
            $msg['mateStatus'][$friend['id']] = '"' . $friend['publicToken'] . '"';
        }
        $this->myZmq->send($msg);

        return $response;
    }
}
