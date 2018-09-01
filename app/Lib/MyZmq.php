<?php
namespace App\Lib;

class MyZmq extends \App\Constructor
{
    public function send($msg)
    {
        if (empty($this->blacklist->getBlacklistById($msg['dest'], $msg['exp'])))
        {
            $socket = $this->zmq;
            $socket->send(json_encode($msg));
            if (!array_key_exists('when', $msg) && !array_key_exists('mateStatus', $msg))
            {
                $notif = array(
                    $msg['dest'],
                    $msg['exp'],
                    $msg['link'],
                    $msg['msg']
                );
                $this->notif->setNotification($notif);
            }
        }
    }
}
