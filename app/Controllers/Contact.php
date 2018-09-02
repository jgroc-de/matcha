<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Contact extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $template = 'templates/logForm/contact.html.twig';
        return $this->view->render($response, $template, [
            'user' => $_SESSION['profil']
        ]);
    }

    public function sendMail(Request $request, Response $response, array $args)
    {
        $post = $request->getParsedBody();
        if ($this->validator->validate($post, ['email', 'text']))
        {
            $this->mail->contactMe($post['text'], $post['email']);
            $this->flash->addMessage('success', 'Thank you!');
        }
        else
            $this->flash->addMessage('fail', 'Thank you for the spam!');
        $template = 'templates/logForm/contact.html.twig';
        return $this->view->render($response, $template, [
                'flash' => $this->flash->getMessages()
        ]);
    }
}
