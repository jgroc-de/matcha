<?php

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class xhrMiddleware extends \App\Constructor
{
    /**
     * middleware that redirect to '/login' if client is not login
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request PSR7 request
     * @param \Psr\Http\Message\ResponseInterface $response PSR7 response
     * @param callable $next Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response, $next): Response
    {
        if (!$request->isXhr()) {
            return $response->withStatus(418);
        }

        return $next($request, $response);
    }
}
