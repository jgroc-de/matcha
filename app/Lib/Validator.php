<?php

namespace App\Lib;

class Validator
{
    /**
     * @var array
     */
    protected $characters = ['Rick', 'Morty', 'Beth', 'Jerry', 'Summer'];

    /**
     * @var array
     */
    protected $sexualPattern = ['bi', 'homo', 'hetero'];

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
                    return false;
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

    public function pseudo($test)
    {
        $len = strlen($test);
        return ($len > 0 && $len < 41) ? true: false;
    }

    public function password($test)
    {
        return (preg_match('#(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}#', $test));
    }

    public function password1($test)
    {
        return (preg_match('#(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}#', $test));
    }

    public function email($test)
    {
        return (preg_match('#^[a-z0-9-_\.]+@[a-z0-9-_\.]{2,}\.[a-z]{2,4}$#', $test));
    }

    public function gender($test)
    {
        return in_array($test, $this->characters);
    }
    
    public function sexuality($test)
    {
        return in_array($test, $this->sexualPattern);
    }
    
    public function birthdate($test)
    {
        return is_numeric($test);
    }
    
    public function name($test)
    {
        return !is_null($test);
    }
    
    public function forname($test)
    {
        return !is_null($test);
    }
    
    public function biography($test)
    {
        return true;
    }

    public function submit($test)
    {
        return ($test === 'Envoyer');
    }
}
