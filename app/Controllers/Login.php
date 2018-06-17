<?php

namespace App;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * class PagesController
 * this class is called by each routes
 */
class Login extends \App\Constructor
{
    /**
     * @param $request requestInterface
     * @param $response responseInterface
     *
     * @return twig view
     */
    public function route(Request $request, Response $response, array $args)
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
