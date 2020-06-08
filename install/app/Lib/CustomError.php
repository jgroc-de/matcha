<?php

namespace App\Lib;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

/**
 * Custom Error handler
 */
class CustomError
{
    /** @var Twig */
    private $view;

    public function __construct(Twig $view)
    {
        $this->view = $view;
    }

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
                    'me' => $_SESSION['profil'] ?? null,
                    'error' => "Error $code: $error",
                ]
            );
    }
}
