<?php

namespace App\Model;

class ContainerClass
{
    /**
     * @var array : for $container
     */
    protected $container;

    /**
     * @param $container array
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param $name string : shortcut to access dependencies in $container
     * @return $container['$name'] : matching class from container if any
     */
    public function __get($name)
    {
//        if (isset($container[$name]))
            return $this->container->get($name);
    }
}
