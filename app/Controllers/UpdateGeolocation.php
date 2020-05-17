<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateGeolocation extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $keys = ['lat', 'lng'];
        $post = $request->getParsedBody();
        if ($this->validator->validate($post, $keys)) {
            if ($this->user->updateGeolocation($post['lat'], $post['lng'], $_SESSION['id'])) {
                $_SESSION['profil']['lattitude'] = floatval($post['lat']);
                $_SESSION['profil']['longitude'] = floatval($post['lng']);
                $response->write(json_encode($post, JSON_NUMERIC_CHECK));

                return $response;
            }

            return $response->withStatus(500);
        }

        return $response->withStatus(400);
    }
}
