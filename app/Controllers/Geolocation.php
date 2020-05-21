<?php

namespace App\Controllers;

use App\Lib\Validator;
use App\Model\UserModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Geolocation
{
    /** @var Validator */
    private $validator;
    /** @var UserModel */
    private $user;

    public function __construct(Validator $validator, UserModel $userModel)
    {
        $this->validator = $validator;
        $this->user = $userModel;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $keys = ['lat', 'lng'];
        $post = $request->getParsedBody();
        if (!$this->validator->validate($post, $keys)) {
            return $response->withStatus(400);
        }
        if (!$this->user->updateGeolocation($post['lat'], $post['lng'], $_SESSION['id'])) {
            return $response->withStatus(404);
        }
        $_SESSION['profil']['lattitude'] = floatval($post['lat']);
        $_SESSION['profil']['longitude'] = floatval($post['lng']);
        $response->write(json_encode($post, JSON_NUMERIC_CHECK));

        return $response;
    }
}
