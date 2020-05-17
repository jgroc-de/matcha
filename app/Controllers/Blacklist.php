<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Blacklist extends Route
{
    public function __invoke(Request $request, Response $response, array $args): int
    {
        $this->friends->delFriend($args['id'], $_SESSION['id']);
        if (!$this->blacklist->getBlacklistById($_SESSION['id'], $args['id'])) {
            $this->blacklist->setBlacklist($args['id']);
            $flash = 'This user is now on your blacklist!';
        } else {
            $flash = 'This user is already on your blacklist!';
        }

        return $response->getBody()->write($flash);
    }
}
