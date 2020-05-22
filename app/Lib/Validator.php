<?php

namespace App\Lib;

/**
 * check data type
 */
class Validator
{
    /** @var array */
    private $post;
    /** @var FlashMessage */
    private $flashMessage;

    public function __construct(FlashMessage $flashMessage)
    {
        $this->flashMessage = $flashMessage;
    }

    public function validate(array $array, array $keys)
    {
        if (!$this->ft_isset($array, $keys)) {
            return false;
        }
        $this->post = $array;
        foreach ($keys as $key) {
            $function = str_replace('-', '_', $key);
            if (is_callable([$this, $function]) && !$this->{$function}($array[$key])) {
                if (empty($this->flashMessage->getMessages())) {
                    $this->flashMessage->addMessage('failure', $key . ' is incorrect');
                }

                return false;
            }
        }

        return true;
    }

    private function ft_isset(array $array, array $keys): bool
    {
        foreach ($keys as $key) {
            if (!isset($array[$key])) {
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
        $len = strlen($test);

        return ($len > 0 && $len < 41) ? true : false;
    }

    private function password(string $test): bool
    {
        return preg_match('#(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}#', $test);
    }

    public function password1(string $test): bool
    {
        return $this->password($test);
    }

    public function password_confirmation(string $confirmation): bool
    {
        if ($this->post['password'] !== $confirmation) {
            $this->flashMessage->addMessage('failure', 'Confirm password doesn\'t match');
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
        return in_array($test, ['Rick', 'Morty', 'Beth', 'Jerry', 'Summer']);
    }

    public function sexuality(string $test): bool
    {
        return in_array($test, ['bi', 'homo', 'hetero']);
    }

    public function birthdate(int $test): bool
    {
        return is_numeric($test) && $test <= (date('Y') - 18) && $test >= 1850;
    }

    public function name(string $test): bool
    {
        $len = strlen($test);

        return $len > 0 && $len < 250;
    }

    public function surname(string $test): bool
    {
        $len = strlen($test);

        return $len > 0 && $len < 250;
    }

    public function biography(string $test): bool
    {
        $len = strlen($test);

        return $len > 0;
    }

    public function text(string $test): bool
    {
        $len = strlen($test);

        return $len > 0;
    }

    public function submit(string $test): bool
    {
        return $test === 'Envoyer';
    }

    public function lat(float $test): bool
    {
        return $test >= -85 && $test <= 85;
    }

    public function lng(float $test): bool
    {
        return $test <= 180 && $test >= -180;
    }

    public function g_recaptcha_response(string $token): bool
    {
        $api_url = 'https://www.google.com/recaptcha/api/siteverify?secret='
            . $_ENV['SECRET_CAPTCHA_KEY'] . '&response='
            . $token
            . '&remoteip=' . $_SERVER['REMOTE_ADDR'];

        $decode = json_decode(file_get_contents($api_url), true);

        if (empty($decode) || $decode['success'] != true) {
            $this->flashMessage->addMessage('failure', 'you\'re a robot, don\'t lie');
            return false;
        }

        return true;
    }
}
