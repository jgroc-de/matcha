<?php

namespace App\Middlewares;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class noAuthMiddleware
{
    /**
     * middleware that redirect to '/' if client is login
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response, $next)
    {
        if (isset($_SESSION['id']))
        {
            return $response->withRedirect('/');
        }
        return $next($request, $response);
    }
}
