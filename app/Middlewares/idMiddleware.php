<?php

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class idMiddleware
{
    /**
     * middleware that block process if id is not integer
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        if (intval($request->getAttribute('route')->getArgument('id'))) {
            return $next($request, $response);
        }

        return $response->withStatus(400);
    }
}
