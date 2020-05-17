<?php

namespace App\Lib;

use App\Constructor;

class MyZmq extends Constructor
{
    public function send(array $msg)
    {
        $socket = $this->zmq;
        if (array_key_exists('mateStatus', $msg) || array_key_exists('profilStatus', $msg)) {
            $socket->send(json_encode($msg));
        } elseif (empty($this->blacklist->getBlacklistById($msg['dest'], $msg['exp']))) {
            $socket->send(json_encode($msg));
            if (!array_key_exists('when', $msg)) {
                $notif = [
                    $msg['dest'],
                    $msg['exp'],
                    $msg['link'],
                    $msg['msg'],
                ];
                $this->notif->setNotification($notif);
            }
        }
    }
}
