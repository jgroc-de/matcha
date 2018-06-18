<?php
namespace App\Lib;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Custom Error handler
 */
class CustomError extends \App\Constructor
{
    /**
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return twig view
     */
    public function __invoke(Request $request, Response $response, $exception = '')
    {
        if (!$exception)
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
