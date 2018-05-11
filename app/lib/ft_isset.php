<?php

/**
 * @param $array array
 * @param $keys ... string can be multiple string
 * return bool
 */
function ft_isset(array $array, ...$keys)
{
    foreach ($keys as $key)
    {
        if (!isset($array[$key]))
            return false;
    }
    return true;
}
