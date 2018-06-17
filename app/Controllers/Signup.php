<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Signup extends \App\Constructor
{
    public function route(Request $request, Response $response, array $args)
    {
        $this->ft_geoIP->setLatLng();
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $this->form->checkSignup($request, $response);
        }
        return $this->view->render(
            $response,
            'templates/logForm/signup.html.twig',
            [
                'characters' => $this>characters,
                'flash' => $this->flash->getMessages(),
                'post' => $_POST
            ]
        );
    }

}
