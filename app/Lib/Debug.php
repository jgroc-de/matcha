<?php

namespace App\Lib;

/**
 * debugging tool
 */
class Debug
{
    /**
     * print array in a nice way.
     */
    public function ft_print(array $array)
    {
        foreach ($array as $key => $value) {
            echo $key . ' => ' . $value . '<br>';
        }
        echo ' ------------------------- <br>';
    }
}
