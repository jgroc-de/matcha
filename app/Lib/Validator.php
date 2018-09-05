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
        if ($this->ft_isset($array, $keys))
        {
            foreach ($keys as $key)
            {
                if (!$this->$key($array[$key]))
                {
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
        foreach ($keys as $key)
        {
            if (!isset($array[$key]))
                return false;
        }
        return true;
    }

    /**
     * @param string $test
     *
     * @return bool
     */
    public function pseudo(string $test)
    {
        $len = strlen($test);
        return ($len > 0 && $len < 41) ? true: false;
    }

    /**
     * @param string $test
     *
     * @return bool
     */
    public function password(string $test)
    {
        return (preg_match('#(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}#', $test));
    }

    /**
     * @param string $test
     *
     * @return bool
     */
    public function password1(string $test)
    {
        return (preg_match('#(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}#', $test));
    }

    /**
     * @param string $test
     *
     * @return bool
     */
    public function email(string $test)
    {
        return (preg_match('#^[a-z0-9-_\.]+@[a-z0-9-_\.]{2,}\.[a-z]{2,4}$#', $test));
    }

    /**
     * @param string $test
     *
     * @return bool
     */
    public function gender(string $test)
    {
        return in_array($test, ['Rick', 'Morty', 'Beth', 'Jerry', 'Summer']);
    }
    
    /**
     * @param string $test
     *
     * @return bool
     */
    public function sexuality(string $test)
    {
        return in_array($test, ['bi', 'homo', 'hetero']);
    }
    
    /**
     * @param int $test
     *
     * @return bool
     */
    public function birthdate(int $test)
    {
        return (is_numeric($test) && $test <= date('Y') && $test >= 1850);
    }
    
    /**
     * @param string $test
     *
     * @return bool
     */
    public function name(string $test)
    {
        return !is_null($test);
    }
    
    /**
     * @param string $test
     *
     * @return bool
     */
    public function surname(string $test)
    {
        return !is_null($test);
    }
    
    /**
     * @param string $test
     *
     * @return bool
     */
    public function biography(string $test)
    {
        return true;
    }

    public function text(string $test)
    {
        return true;
    }
    /**
     * @param string $test
     *
     * @return bool
     */
    public function submit(string $test)
    {
        return ($test === 'Envoyer');
    }

    /**
     * @param float $test
     *
     * @return bool
     */
    public function lat(float $test)
    {
        return ($test >= -85 && $test <= 85);
    }

    /**
     * @param float $test
     *
     * @return bool
     */
    public function lng(float $test)
    {
        return ($test <= 180 && $test >= -180);
    }
}
