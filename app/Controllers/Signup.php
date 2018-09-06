<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Signup extends Route
{
    private $post = array();
    
    public function __invoke(Request $request, Response $response, array $args)
    {
        return $this->view->render(
            $response,
            'templates/logForm/login.html.twig',
            [
                'characters' => $this->characters,
                'flash' => $this->flash->getMessages(),
                'post' => $this->post,
                'signup' => true
            ]
        );
    }

    public function check(Request $request, Response $response, array $args)
    {
        $this->post = $request->getParsedBody();
        $this->post = $this->form->checkSignup($this->post);
        $this($request, $response, $args);
    }
}
