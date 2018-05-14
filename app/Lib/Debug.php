<?php

namespace App\Lib;

/**
 * class debug
 */
class Debug
{
    /**
     * @param array $array
     */
    public function ft_print(array $array)
    {
        foreach ($array as $key => $value)
        {
            echo $key . ' => ' . $value . '<br>';
        }
        echo ' ------------------------- <br>';
    }
}
