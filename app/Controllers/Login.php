<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Login extends Route
{
    private $post = [];

    public function __invoke(Request $request, Response $response, array $args)
    {
        if (!isset($post['gender'])) {
            $post['gender'] = $this->characters[random_int(0, 4)];
        }

        return $this->view->render(
            $response,
            'templates/logForm/login.html.twig',
            [
                'flash' => $this->flash->getMessages(),
                'post' => $post,
                'login' => true,
                'characters' => $this->characters,
            ]
        );
    }

    public function check(Request $request, Response $response, array $args)
    {
        $this->post = $request->getParsedBody();
        if ($this->form->checkLogin($this->post)) {
            return $response->withRedirect('/');
        }
        $this($request, $response, $args);
    }
}
