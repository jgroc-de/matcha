<?php

namespace App\Lib;

/**
 * check data type
 */
class Validator
{
    /**
     * @param $array array
     * @param $keys ... string can be multiple string
     * return bool
     */
    public function validate(array $array, array $keys)
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

    /**
     * @param $array array
     * @param $keys array
     * return bool
     */
    public function ft_isset(array $array, array $keys)
    {
        foreach ($keys as $key) {
            if (!isset($array[$key])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function pseudo(string $test)
    {
        $len = strlen($test);

        return ($len > 0 && $len < 41) ? true : false;
    }

    /**
     * @return bool
     */
    public function password(string $test)
    {
        return preg_match('#(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}#', $test);
    }

    /**
     * @return bool
     */
    public function password1(string $test)
    {
        return preg_match('#(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}#', $test);
    }

    /**
     * @return bool
     */
    public function email(string $test)
    {
        return preg_match('#^[a-z0-9-_\.]+@[a-z0-9-_\.]{2,}\.[a-z]{2,4}$#', $test);
    }

    /**
     * @return bool
     */
    public function gender(string $test)
    {
        return in_array($test, ['Rick', 'Morty', 'Beth', 'Jerry', 'Summer']);
    }

    /**
     * @return bool
     */
    public function sexuality(string $test)
    {
        return in_array($test, ['bi', 'homo', 'hetero']);
    }

    /**
     * @return bool
     */
    public function birthdate(int $test)
    {
        return is_numeric($test) && $test <= (date('Y') - 18) && $test >= 1850;
    }

    /**
     * @return bool
     */
    public function name(string $test)
    {
        $len = strlen($test);

        return $len > 0 && $len < 250;
    }

    /**
     * @return bool
     */
    public function surname(string $test)
    {
        $len = strlen($test);

        return $len > 0 && $len < 250;
    }

    /**
     * @return bool
     */
    public function biography(string $test)
    {
        $len = strlen($test);

        return $len > 0;
    }

    public function text(string $test)
    {
        $len = strlen($test);

        return $len > 0;
    }

    /**
     * @return bool
     */
    public function submit(string $test)
    {
        return $test === 'Envoyer';
    }

    /**
     * @return bool
     */
    public function lat(float $test)
    {
        return $test >= -85 && $test <= 85;
    }

    /**
     * @return bool
     */
    public function lng(float $test)
    {
        return $test <= 180 && $test >= -180;
    }
}
