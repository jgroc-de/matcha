<?php

namespace App\Lib;

use App\Constructor;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Custom Error handler
 */
class CustomError extends Constructor
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request PSR7 request
     * @param \Psr\Http\Message\ResponseInterface $response PSR7 response
     * @param callable $next Next middleware
     * @param mixed $exception
     *
     * @return twig view
     */
    public function __invoke(Request $request, Response $response, $exception = '')
    {
        if (!$exception) {
            $code = 404;
            $error = 'not found…';
        } else {
            $code = 405;
            $error = 'method not allowed…';
        }

        return $this
            ->container
            ->view
            ->render(
                $response->withStatus($code),
                'templates/error.html.twig',
                [
                    'error' => "Error $code: $error",
                ]
            );
    }
}
