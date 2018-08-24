<?php
namespace App\Lib;

class MyZmq extends \App\Constructor
{
    public function send($msg)
    {
        $socket = $this->zmq;
        $socket->send(json_encode($msg));
    }
}
