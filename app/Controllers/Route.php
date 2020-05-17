<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * constructor for each route.
 */
abstract class Route extends \App\Constructor
{
    /**
     * @param Request $request RequestInterface
     * @param Respone $response ResponseInterface
     */
    abstract public function __invoke(Request $request, Response $response, array $args);
}
