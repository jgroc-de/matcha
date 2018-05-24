<?php

namespace App\Lib;

/**
 * class Custom404Error
 */
class CustomError extends \App\Constructor
{
    public function __invoke($request, $response, $extra)
    {
        if (!isset($extra))
        {
            $code = 404;
            $error = 'not found…';
        }
        else
        {
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
                    'error' => "Error $code: $error"
                ]
            );
    }
}
