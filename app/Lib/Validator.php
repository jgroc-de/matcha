<?php

namespace App\Lib;

/**
 * check data type
 */
class Validator
{
    public function validate(array $array, array $keys): bool
    {
        if ($this->ft_isset($array, $keys)) {
            foreach ($keys as $key) {
                if (is_callable([$this, $key]) && !$this->{$key}($array[$key])) {
                    return false;
                }
            }

            return true;
        }

        return false;
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
}
