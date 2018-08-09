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
            'category' => 'msg',
            'exp'    => $_SESSION['id'],
            'dest'    => $_POST['id'],
            'msg'  => $_POST['msg'],
            'myId'    => $_SESSION['id'],
            'when'     => time()
        );
        if ($this->friends->isFriend($tab))
        {
            $socket = $this->zmq;
            
            $socket->send(json_encode($msg));
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
        $msgs = $this->msg->getMessages($tab);
        $response->getBody()->write(json_encode($msgs));
        return $response;
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
}
