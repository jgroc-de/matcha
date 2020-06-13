<?php

namespace App\Lib;

/**
 * managing flash message
 */
class FlashMessage
{
    const SUCCESS = 'success';
    const FAIL = 'failure';

    /** @var int $id */
    private $id = 1;

    /** @var array $storage */
    private $storage = [];

    /**
     * @param string $key key = FlashMessage::SUCCESS or FAIL
     */
    public function addMessage(string $key, string $message)
    {
        if ($key === self::SUCCESS) {
            $this->storage[self::SUCCESS] = $message;
        } else {
            if (!isset($this->storage[self::FAIL])) {
                $this->storage[self::FAIL] = [];
            }
            $this->storage[self::FAIL][$this->id] = $message;
            ++$this->id;
        }
    }

    /**
     * @return array all stored message
     */
    public function getMessages(): array
    {
        return $this->storage;
    }
}
