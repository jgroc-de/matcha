<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * constructor for each route.
 */
abstract class Route extends \App\Constructor
{
    /**
     * @param Request $request RequestInterface
     * @param Respone $response ResponseInterface
     * @param array $args
     */
    abstract protected function route(Request $request, Response $response, array $args);
}
