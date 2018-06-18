<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Login extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $this->form->checkLogin($request, $response);
        if (isset($_SESSION['id']))
            return $response->withRedirect('/');
        return $this->view->render(
            $response,
            'templates/logForm/login.html.twig',
            [
                'flash' => $this->flash->getMessages(),
                'post' => $_POST
            ]
        );
    }
}
