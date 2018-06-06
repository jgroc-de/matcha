<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * class PagesController
 * this class is called by each routes
 */
class LogInController extends \App\Constructor
{
    /**
     * @param $request requestInterface
     * @param $response responseInterface
     * @return twig view
     */
    public function signup (Request $request, Response $response)
    {
        $this->ft_geoIP->setLatLng();
        $this->debug->ft_print($_POST);
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $this->form->checkSignup($request, $response);
        }
        return $this->view->render(
            $response,
            'templates/logForm/signup.html.twig',
            [
                'characters' => $this->characters,
                'flash' => $this->flash->getMessages(),
                'post' => $_POST
            ]
        );
    }

    /**
     * validation of an account
     *
     * @param $request requestInterface
     * @param $response responseInterface
     * @return redirection to home
     */
    public function validation (Request $request, Response $response)
    {
        $get = $request->getParams();
        $account = $this->user->getUser($get['login']);
        if (!empty($account) && $get['token'] = $account['token'])
        {
            $_SESSION['pseudo'] = $account['pseudo'];
            $_SESSION['id'] = $account['id'];
            $_SESSION['profil'] = $account;
            if ($get['action'] === 'reinit')
                return $response->withRedirect('/password');
            $this->user->activate();
        }
        return $response->withRedirect('/');
    }

    /**
     * reinit password
     *
     * @param $request requestInterface
     * @param $response responseInterface
     * @return redirection to home
     */
    public function resetPassword (Request $request, Response $response)
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

    /**
     * @param $request requestInterface
     * @param $response responseInterface
     * @return twig view
     */
    public function login (Request $request, Response $response)
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
    
    /**
     * @param $request requestInterface
     * @param $response responseInterface
     * @return twig view
     */
    public function logout (Request $request, Response $response)
    {
        session_destroy();
        return $response->withRedirect('/login');
    }
}
