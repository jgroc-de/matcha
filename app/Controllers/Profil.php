<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Profil extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $user = $this->user->getUserById($args['id']);
        if ($user)
        {
            if ($user['id'] != $_SESSION['id'])
            {
                $msg = array(
                    "category" => '"' . $user['publicToken'] . '"',
                    "iduser" => $user['id'],
                    "link" => "/profil/" . $_SESSION['id'],
                    "msg" => $_SESSION['profil']['pseudo'] . ' watched your profil!'
                );
                $this->MyZmq->send($msg);
                $friends = $this->friends->getFriends($_SESSION['id']);
                $friend = false;
                foreach ($friends as $test)
                {
                    if ($test['id'] === $user['id'])
                    {
                        $friend = true;
                        break;
                    }
                }
            }
            return $this->view->render(
                $response,
                'templates/home/profil.html.twig',
                [
                    'friend' => $friend,
                    'profil' => $user,
                    'me' => $_SESSION['profil'],
                    'tags' => $this->tag->getUserTags($user['id']),
                    'notification' => $this->notif->getNotification()
                ]
            );
        }
    }
}
