<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Contact extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $user = array();
        if (isset($_SESSION['profil']))
            $user = $_SESSION['profil'];
        $template = 'templates/logForm/contact.html.twig';
        return $this->view->render($response, $template, [
            'user' => $user,
            'flash' => $this->flash->getMessages()
        ]);
    }

    public function sendMail(Request $request, Response $response, array $args)
    {
        $post = $request->getParsedBody();
        $this->form->checkContact($post);
        $this($request, $response, $args);
    }
}
