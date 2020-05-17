<?php

namespace App\Lib;

/**
 * managing flash message
 */
class FlashMessage
{
    /** @var int $id */
    private $id;

    /** @var array $storage */
    private $storage;

    public function __construct()
    {
        $this->storage = [];
        $id = 1;
    }

    /**
     * @param string $key key = fail or success
     */
    public function addMessage(string $key, string $message)
    {
        if ($key === 'success') {
            $this->storage[$key] = $message;
        } else {
            $this->storage[$this->id] = $message;
            ++$this->id;
        }
    }

    /**
     * @return array all stored message
     */
    public function getMessages()
    {
        return $this->storage;
    }
}
