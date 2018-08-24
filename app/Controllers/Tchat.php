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
        $msg = array(
            'category' => $_POST['token'],
            'exp'    => $_SESSION['id'],
            'dest'    => $_POST['id'],
            'msg'  => $_POST['msg'],
            'myId'    => $_SESSION['id'],
            'when'     => time()
        );
        if ($this->friends->isFriend($tab))
        {
            $this->MyZmq->send($msg);
            $this->msg->setMessage(array($tab[0], $tab[1], $_SESSION['id'], $msg['msg'], date('Y-m-d H:i:s')));
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
                'friends' => $friends
            ]
        );
    }

    public function status()
    {
        $friends = $this->friends->getFriends($_SESSION['id']);
        $msg = array(
            'category' => '"' . $_SESSION['profil']['publicToken'] . '"',
            'status' => array()
        );
        foreach ($friends as $friend)
        {
            $msg['status'][$friend['id']] = '"' . $friend['publicToken'] . '"';
        }
        $this->MyZmq->send($msg);
        return $response;
    }
}
