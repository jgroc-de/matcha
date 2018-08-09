<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class ResetPassword extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']))
        {
            $user = $this->container->user;
            $account = $user->getUserByEmail($_POST['email']);
            if ($account)
            {
                $account['token'] = password_hash(random_bytes(6), PASSWORD_DEFAULT);
                $user->updateToken($account['pseudo'], $account['token']);
                if($this->mail->sendResetMail($account['pseudo'], $account['email'], $account['token']))
                    $this->flash->addMessage('success', 'Check your mail!');
                else
                    $this->flash->addMessage('failure', 'Mail not sent');
            }
            else
                $this->flash->addMessage('failure', 'unknown mail address…');
        }
        return $this->view->render(
            $response,
            'templates/logForm/login.html.twig',
            [
                'characters' => $this->characters,
                'flash' => $this->flash->getMessages(),
                'post' => $_POST,
                'reset' => true,
            ]
        );
    }
}
