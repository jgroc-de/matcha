<?php
namespace App\Lib;

class MyZmq extends \App\Constructor
{
    public function send($msg)
    {
        $socket = $this->zmq;
        $socket->send(json_encode($msg));
        if (!array_key_exists('exp', $msg) && !array_key_exists('mateStatus', $msg))
        {
            $notif = array(
                $msg['iduser'],
                $msg['link'],
                $msg['msg']
            );
            $this->notif->setNotification($notif);
        }
    }
}
