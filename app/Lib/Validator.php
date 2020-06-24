<?php

namespace App\Lib;

/**
 * check data type
 */
class Validator
{
    /** @var array of all kind available */
    const GENDER = ['Rick', 'Morty', 'Beth', 'Jerry', 'Summer'];
    /** @var array all orientation available */
    const KIND = ['bi', 'homo', 'hetero'];
    const PSEUDO = '[^\s\'"`]{1,42}';
    const PASSWORD = '(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}';
    const MAX_AGE = 100;
    const MIN_AGE = 18;
    const MAX_TEXT_LENGTH = 1500;
    const MAX_NAME_LENGTH = 250;
    const MAX_LNG = 180;
    const MIN_LNG = -180;
    const MAX_LAT = 85;
    const MIN_LAT = -85;

    /** @var array */
    private $post;
    /** @var FlashMessage */
    private $flashMessage;

    public function __construct(FlashMessage $flashMessage)
    {
        $this->flashMessage = $flashMessage;
    }

    public function validate(?array $array, ?array $keys)
    {
        if (!$this->ft_isset($array, $keys)) {
            return false;
        }
        $this->post = $array;
        foreach ($keys as $key) {
            $function = str_replace('-', '_', $key);
            if (is_callable([$this, $function]) && !$this->{$function}($array[$key])) {
                if (empty($this->flashMessage->getMessages())) {
                    $this->flashMessage->addMessage(FlashMessage::FAIL, $key . ' is incorrect');
                }

                return false;
            }
        }

        return true;
    }

    private function ft_isset(?array $array, ?array $keys): bool
    {
        foreach ($keys as $key) {
            if (!isset($array[$key])) {
                $this->flashMessage->addMessage(FlashMessage::FAIL, $key . ' is missing');
                return false;
            }
        }

        return true;
    }

    /**
     * magic call by validate method
     */
    private function pseudo(string $test): bool
    {
        if (!preg_match('#' . self::PSEUDO . '#', $test)) {
            return false;
        }

        return true;
    }

    private function password(string $test): bool
    {
        return preg_match('#' . self::PASSWORD . '#', $test);
    }

    public function password1(string $test): bool
    {
        return $this->password($test);
    }

    public function password_confirmation(string $confirmation): bool
    {
        if ($this->post['password'] !== $confirmation) {
            $this->flashMessage->addMessage(FlashMessage::FAIL, 'Confirm password doesn\'t match');

            return false;
        }

        return true;
    }

    public function email(string $test): bool
    {
        return preg_match('#^[a-z0-9-_\.]+@[a-z0-9-_\.]{2,}\.[a-z]{2,4}$#', $test);
    }

    public function gender(string $test): bool
    {
        return in_array($test, self::GENDER);
    }

    public function sexuality(string $test): bool
    {
        return in_array($test, self::KIND);
    }

    public function birthdate($test): bool
    {
        return is_numeric($test) && $test <= (date('Y') - self::MIN_AGE) && $test >= (date('Y') - self::MAX_AGE);
    }

    public function name(string $test): bool
    {
        $len = strlen($test);

        return $len > 0 && $len < self::MAX_NAME_LENGTH;
    }

    public function surname(string $test): bool
    {
        $len = strlen($test);

        return $len > 0 && $len < self::MAX_NAME_LENGTH;
    }

    public function biography(string $test): bool
    {
        $len = strlen($test);

        return $len > 0 && $len < self::MAX_TEXT_LENGTH;
    }

    public function text(string $test): bool
    {
        $len = strlen($test);

        return $len > 0;
    }

    public function lat($test): bool
    {
        $test = (float) $test;
        return $test >= self::MIN_LAT && $test <= self::MAX_LAT;
    }

    public function lng($test): bool
    {
        $test = (float) $test;
        return $test <= self::MAX_LNG && $test >= self::MIN_LNG;
    }

    public function g_recaptcha_response(string $token): bool
    {
        $api_url = 'https://www.google.com/recaptcha/api/siteverify?secret='
            . $_ENV['SECRET_CAPTCHA_KEY'] . '&response='
            . $token
            . '&remoteip=' . $_SERVER['REMOTE_ADDR'];

        $decode = json_decode(file_get_contents($api_url), true);

        if (empty($decode) || $decode[FlashMessage::SUCCESS] != true) {
            $this->flashMessage->addMessage(FlashMessage::FAIL, 'you\'re a robot, don\'t lie');

            return false;
        }

        return true;
    }
}
