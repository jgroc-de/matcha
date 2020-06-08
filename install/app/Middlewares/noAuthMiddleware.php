<?php

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class noAuthMiddleware
{
    /**
     * middleware that redirect to '/' if client is login
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        if (isset($_SESSION['id'])) {
            return $response->withRedirect('/');
        }

        return $next($request, $response);
    }
}
