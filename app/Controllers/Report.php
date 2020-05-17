<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Report extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $this->friends->delFriend($args['id'], $_SESSION['id']);
        if (!$this->blacklist->getBlacklistById($_SESSION['id'], $args['id'])) {
            $this->blacklist->setBlacklist($args['id']);
        }
        $this->mail->reportMail($args['id']);

        return $response->getBody()->write('Thank you to help us improved the community!');
    }
}
