<?php

namespace App\Lib;

/**
 * class FlashMessage
 */
class FlashMessage
{
    /**
     * @var int $id
     */
    private $id;

    /**
     * @var array $storage
     */
    private $storage;

    public function __construct ()
    {
        $this->storage = array();
        $id = 1;
    }

    public function addMessage ($key, $message)
    {
        if ($key === 'success')
            $this->storage[$key] = $message;
        else
        {
            $this->storage[$this->id] = $message;
            $this->id += 1;
        }
    }

    public function getMessages ()
    {
        return $this->storage;
    }
}
