<?php

namespace App\Controllers;

use App\Constructor;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * constructor for each route.
 */
abstract class Route extends Constructor
{
    abstract public function __invoke(Request $request, Response $response, array $args);
}
