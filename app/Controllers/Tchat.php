<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Tchat extends Route
{
    public function send(Request $request, Response $response, array $args)
    {
        $tab = array($_SESSION['id'], $_POST['id']);
        sort($tab);
        if ($this->friends->isFriend($tab))
        {
            $msg = array(
                'category' => $_POST['token'],
                'exp'    => $_SESSION['id'],
                'dest'    => $_POST['id'],
                'msg'  => $_POST['msg'],
                'myId'    => $_SESSION['id'],
                'when'     => time()
            );
            $this->MyZmq->send($msg);
            $this->msg->setMessage(array($tab[0], $tab[1], $_SESSION['id'], $msg['msg'], date('Y-m-d H:i:s')));
           if ($_POST['id'] < 500)
            {
                $chat = array(
                    'Jeeeezz… another dumbass pervert?',
                    'U want a pic of my dick?',
                    'Hi sweetheart',
                    'Stop stalking around, go watch TV!',
                    'You fight like a Dairy Farmer!',
                    'This is the END for you, you gutter crawling cur!',
                    "I've spoken with apes more polite than you!",
                    "Soon you'll be wearing my sword like a shish kebab!",
                    "People fall at my feet when they see me coming!",
                    "I'm not going to take your insolence sitting down!",
                    "I once owned a dog that was smarter than you.",
                    "Nobody's ever drawn blood from me and nobody ever will.",
                    "Have you stopped wearing diapers yet?",
                    "There are no words for how disgusting you are.",
                    "You make me want to puke.",
                    "My handkerchief will wipe up your blood!",
                    "I got this scar on my face during a mighty struggle!",
                    "I've heard you are a contemptible sneak.",
                    "You're no match for my brains, you poor fool.",
                    "You have the manners of a beggar. "
                );
                $msg = array(
                    'category' => $_POST['token'],
                    'exp'    => $_POST['id'],
                    'dest'    => $_SESSION['id'],
                    'msg'  => $chat[rand(0, 20)],
                    'myId'    => $_POST['id'],
                    'when'     => time()
                );
                $this->MyZmq->send($msg);
                $this->msg->setMessage(array($tab[0], $tab[1], $_POST['id'], $msg['msg'], date('Y-m-d H:i:s')));
            }
            return $response->withstatus(200);
        }
        else
        {
            return $response->withstatus(403);
        }
    }

    public function startTchat(Request $request, Response $response, array $args)
    {
        $tab = array($_SESSION['id'], $_POST['id']);
        sort($tab);
        if ($this->friends->isFriend($tab))
        {
            $user = $this->user->getUserById($_POST['id']);
            $msg = array(
                'category' => '"' . $user['publicToken'] .'"',
                'iduser' => $_POST['id'],
                'msg' => $_SESSION['profil']['pseudo'] . ' sent you a new message!',
                'link' => '/tchat'
            );
            $this->MyZmq->send($msg);
            $msgs = $this->msg->getMessages($tab);
            $response->getBody()->write(json_encode($msgs));
            return $response;
        }
        else
        {
            return $response->withstatus(403);
        }
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
        $friends = $this->friends->getFriends($_SESSION['id']);
        return $this->view->render(
            $response,
            'templates/home/tchat.html.twig',
            [
                'me' => $_SESSION['profil'],
                'notification' => $this->notif->getNotification(),
                'friends' => $friends
            ]
        );
    }

    public function status()
    {
        $friends = $this->friends->getFriends($_SESSION['id']);
        $msg = array(
            'category' => '"' . $_SESSION['profil']['publicToken'] . '"',
            'mateStatus' => array()
        );
        foreach ($friends as $friend)
        {
            $msg['mateStatus'][$friend['id']] = '"' . $friend['publicToken'] . '"';
        }
        $this->MyZmq->send($msg);
    }
}
