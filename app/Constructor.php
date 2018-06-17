<?php

namespace App;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

abstract class Constructor
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
     * @var \Slim\Container : for $container
     */
    protected $container;

    /**
     * @param \Slim\Container $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param $request RequestInterface
     * @param $response ResponseInterface
     * @param $args array
     *
     * @return $response ResponseInterface
     */
    abstract protected function route(Request $request, Response $response, array $args);

    /**
     * @param $name string : shortcut to access dependencies in $container
     * @return $container['$name'] : matching class from container if any
     */
    public function __get($name)
    {
        if (isset($this->container[$name]))
            return $this->container->get($name);
    }
}
