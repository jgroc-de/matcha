<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * class PagesController
 * this class is called by each __invokes
 */
class Logout extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        session_unset();
        session_destroy();
        return $response->withRedirect('/login');
    }
}