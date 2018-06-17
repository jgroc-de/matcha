<?php

namespace App;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * class ResetPassword
 * this class is called by each routes
 */
class ResetPassword extends \App\Constructor
{
    /**
     * @param $request RequestInterface
     * @param $response ResponseInterface
     * @param $args array
     *
     * @return twigview
     */
    public function route(Request $request, Response $response)
    {
        $user = $this->container->user;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']))
        {
            if (!empty(($account = $user->getUserByEmail($_POST['email']))))
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
