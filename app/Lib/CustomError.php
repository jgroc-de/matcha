<?php

namespace App\Lib;

use App\Constructor;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

/**
 * Custom Error handler
 */
class CustomError extends Constructor
{
    public function __invoke(Request $request, Response $response, string $exception = ''): Response
    {
        if (!$exception) {
            $code = 404;
            $error = 'not found…';
        } else {
            $code = 405;
            $error = 'method not allowed…';
        }

        return $this->view
            ->render(
                $response->withStatus($code),
                'templates/error.html.twig',
                [
                    'error' => "Error $code: $error",
                ]
            );
    }
}
