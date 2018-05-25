<?php

namespace App\Lib;

/**
 * class FlashMessage
 */
class FlashMessage
{
    /**
     * @var $storage array
     */
    protected $storage;

    /**
     * @param array $array
     */
    public function __construct ()
    {
        $this->storage = array();
    }

    /**
     * @param string $key
     * @param string $message
     */
    public function addMessage ($key, $message)
    {
        $this->storage[$key] = $message;
    }

    /**
     * return $storage array
     */
    public function getMessages()
    {
        return $this->storage;
    }
}
