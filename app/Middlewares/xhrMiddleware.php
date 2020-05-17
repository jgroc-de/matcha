<?php

namespace App\Middlewares;

use App\Constructor;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class xhrMiddleware extends Constructor
{
    /**
     * middleware that return 418 if not xhr
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        if (!$request->isXhr()) {
            return $response->withStatus(418);
        }

        return $next($request, $response);
    }
}
