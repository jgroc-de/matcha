<?php

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class noAuthMiddleware
{
    /**
     * middleware that redirect to '/' if client is login
     *
     * @param callable $next Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response, $next): Response
    {
        if (isset($_SESSION['id'])) {
            return $response->withRedirect('/');
        }

        return $next($request, $response);
    }
}
