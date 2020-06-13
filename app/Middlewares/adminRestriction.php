<?php


namespace App\Middlewares;


use Slim\Http\Request;
use Slim\Http\Response;

class adminRestriction
{
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        if ($_ENV['PROD'] && !(isset($_SESSION['profil']['email']) && $_SESSION['profil']['email'] === 'jgroc-de@student.42.fr')) {
            return $response->withRedirect('/');
        }

        return $next($request, $response);
    }
}