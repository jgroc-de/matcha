<?php


namespace App\Middlewares;


use Slim\Http\Request;
use Slim\Http\Response;

class adminRestriction
{
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        if ($_ENV['PROD']) {
            return $response->withRedirect('/');
        }

        return $next($request, $response);
    }
}