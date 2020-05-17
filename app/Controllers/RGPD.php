<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RGPD extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        return $this->view->render(
            $response,
            'templates/home/editProfil.html.twig',
            [
                'me' => $_SESSION['profil'],
                'characters' => $this->characters,
                'sexualPattern' => $this->sexualPattern,
                'flash' => $this->flash->getMessages(),
                'year' => date('Y') - 18,
                'notification' => $this->notif->getNotification(),
                'rgpd' => true,
            ]
        );
    }

    public function deleteAccount(Request $request, Response $response, array $args)
    {
        //if ($this->mail->sendDeleteMail())
        if (true) {
            return $response->getBody()->write('Check your mailbox!');
        }

        return $response->getBody()->write('there is a bug… plz contact us, we ill answer asap!');
    }

    public function getAllDatas(Request $request, Response $response, array $args)
    {
        $data = [];
        $data['tag'] = $this->tag->getAllUserTags($_SESSION['id']);
        $data['message'] = $this->msg->getAllMessages();
        $data['notif'] = $this->notif->getAllNotification();
        $data['friends'] = $this->friends->getAllFriends($_SESSION['id']);
        $data['friendsReq'] = $this->friends->getAllFriendsReqs();
        $data['blacklist'] = $this->blacklist->getAllBlacklist();
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $data[$key] = $this->implodeData($value);
            } else {
                $data[$key] = ['empty'];
            }
        }
        $data = array_merge($data, $this->splitImg($this->user->getAllDatas()));
        if ($this->mail->sendDataMail($data)) {
            return $response->getBody()->write('Check your mailbox!');
        }

        return $response->getBody()->write('there is a bug… plz contact us!');
    }

    private function implodeData($data)
    {
        $str = [];
        foreach ($data as $value) {
            $str[] = implode(' - ', $value);
        }

        return $str;
    }

    private function splitImg($data)
    {
        $tab = [];
        $img = [];
        foreach ($data as $key => $value) {
            if (!strncmp($value, '/user_img', 9)) {
                $img[] = $value;
                unset($data[$key]);
            } elseif (!strncmp($key, 'img', 3)) {
                unset($data[$key]);
            }
        }
        $tab['user'] = $data;
        $tab['img'] = $img;

        return $tab;
    }
}
