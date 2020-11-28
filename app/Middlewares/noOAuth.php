<?php


namespace App\Middlewares;


use Slim\Http\Request;
use Slim\Http\Response;

class noOAuth
{
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        if ($_SESSION['profil']['oauth']) {
            return $response->withRedirect('/editProfil');
        }

        return $next($request, $response);
    }
}