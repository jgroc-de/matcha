<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Profil extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        if ($args['id'] == $_SESSION['id']) {
            return $response->withRedirect('/', 302);
        }
        if (!$this->container->blacklist->getBlacklistById($args['id'], $_SESSION['id'])
            && $user = $this->user->getUserById($args['id'])) {
            $msg = [
                'category' => '"' . $user['publicToken'] . '"',
                'dest' => $user['id'],
                'exp' => $_SESSION['id'],
                'link' => '/profil/' . $_SESSION['id'],
                'msg' => $_SESSION['profil']['pseudo'] . ' watched your profil!',
            ];
            $this->MyZmq->send($msg);
            $friend = empty($this->friends->getFriend($_SESSION['id'], $user['id'])) ? false : true;
            $user['lastlog'] = date('d M Y', $user['lastlog']);

            return $this->view->render(
                $response,
                'templates/home/profil.html.twig',
                [
                    'friend' => $friend,
                    'profil' => $user,
                    'me' => $_SESSION['profil'],
                    'tags' => $this->tag->getUserTags($user['id']),
                    'notification' => $this->notif->getNotification(),
                ]
            );
        }

            ($this->container->notFoundHandler)($request, $response);
    }
}
