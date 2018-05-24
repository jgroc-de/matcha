<?php

namespace App\Middlewares;

class authMiddleware
{
    public function __invoke($request, $response, $next)
    {
        if (!isset($_SESSION['id']))
        {
            return $response->withRedirect('/login');
        }
        return $next($request, $response);
    }
}
