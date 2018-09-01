<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Report extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $this->mail->reportMail($args['id']);
        return $response->getBody()->write('Thank you to help us improved the community!');
    }
}
