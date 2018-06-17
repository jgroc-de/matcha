<?php

namespace App;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class ResetPassword extends \App\Constructor
{
    public function route(Request $request, Response $response, array $args)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']))
        {
            $user = $this->container->user;
            $account = $user->getUserByEmail($_POST['email']);
            if ($account)
            {
                $account['token'] = password_hash(random_bytes(6), PASSWORD_DEFAULT);
                $user->updateToken($account['pseudo'], $account['token']);
                $this->mail->sendResetMail($account['pseudo'], $account['email'], $account['token']);
                $this->flash->addMessage('success', 'Check your mail!');
            }
            else
                $this->flash->addMessage('failure', 'unknown mail address…');
        }
        return $this->view->render(
            $response,
            'templates/logForm/resetPassword.html.twig',
            [
                'flash' => $this->flash->getMessages(),
                'post' => $_POST
            ]
        );
    }
}
