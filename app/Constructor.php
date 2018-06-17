<?php

namespace App;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * constructor for each route.
 */
abstract class Constructor
{
    /**
     * @var array all kind available
     */
    protected $characters = ['Rick', 'Morty', 'Beth', 'Jerry', 'Summer'];

    /**
     * @var array all orientation available
     */
    protected $sexualPattern = ['bi', 'homo', 'hetero'];

    /**
     * @var \Slim\Container for $container
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
     * @param Request $request RequestInterface
     * @param Respone $response ResponseInterface
     * @param array $args
     */
    abstract protected function route(Request $request, Response $response, array $args);

    /**
     * @param string $name shortcut to access dependencies in $container
     * @return $container['$name'] : matching class from container if any
     */
    public function __get($name)
    {
        if (isset($this->container[$name]))
            return $this->container->get($name);
    }
}
