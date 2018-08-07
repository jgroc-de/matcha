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
        $msg = $_POST['msg'];
        if ($this->friends->isFriend($tab))
        {
            $this->msg->setMessage(array($tab[0], $tab[1], $_SESSION['id'], $msg, date('Y-m-d H:i:s')));
            return $response->getBody()->write($msg);
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
}
