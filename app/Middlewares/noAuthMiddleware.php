<?php

namespace App\Middlewares;

class noAuthMiddleware
{
    public function __invoke($request, $response, $next)
    {
        if (isset($_SESSION['id']))
        {
            return $response->withRedirect('/');
        }
        return $next($request, $response);
    }
}
