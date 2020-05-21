<?php

namespace App\Lib;

use App\Model\BlacklistModel;
use App\Model\NotificationModel;
use ZMQSocket;

class MyZmq
{
    /** @var ZMQSocket */
    private $zmq;
    /** @var BlacklistModel */
    private $blacklist;
    /** @var NotificationModel */
    private $notif;

    public function __construct(ZMQSocket $zmq, BlacklistModel $blacklist, NotificationModel $notif)
    {
        $this->zmq = $zmq;
        $this->blacklist = $blacklist;
        $this->notif = $notif;
    }

    public function send(array $msg)
    {
        if (array_key_exists('mateStatus', $msg) || array_key_exists('profilStatus', $msg)) {
            $this->zmq->send(json_encode($msg));
        } elseif (empty($this->blacklist->getBlacklistById($msg['exp'], $msg['dest']))) {
            $this->zmq->send(json_encode($msg));
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
