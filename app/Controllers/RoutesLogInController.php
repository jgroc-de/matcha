<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * class PagesController
 * this class is called by each routes
 */
class RoutesLogInController extends \App\Constructor
{
    /**
     * @param $request requestInterface
     * @param $response responseInterface
     * @return twig view
     */
    public function signup (request $request, response $response)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $this->form->checkSignup($request, $response);
            return $response->withRedirect('/login');
        }
        else
        {
            return $this->view->render(
                $response,
                'templates/logForm/signup.html.twig',
                [
                    'characters' => $this->characters
                ]
            );
        }
    }

    /**
     * validation of an ccount
     *
     * @param $request requestInterface
     * @param $response responseInterface
     * @return redirection to home
     */
    public function validation (request $request, response $response)
    {
        $get = $request->getParams();
        $account = $this->user->getUser($get['login']);
        if (!empty($account) && $get['token'] = $account['token'])
        {
            $_SESSION['pseudo'] = $account['pseudo'];
            $_SESSION['id'] = $account['id'];
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
    public function resetPassword (request $request, response $response)
    {
        $user = $this->container->user;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']))
        {
            if (!empty(($account = $user->getUserByEmail($_POST['email']))))
            {
                $account['token'] = password_hash(random_bytes(6), PASSWORD_DEFAULT);
                $user->updateToken($account['pseudo'], $account['token']);
                $this->mail->sendResetMail($account['pseudo'], $account['email'], $account['token']);
                $this->flash->addMessage('Success', 'Seems good, mail sent');
            }
            else
                $this->flash->addMessage('Failure', "It's a major fail… unknown mail");
        }
        return $this->view->render($response, 'templates/logForm/resetPassword.html.twig');
    }

    /**
     * @param $request requestInterface
     * @param $response responseInterface
     * @return twig view
     */
    public function login (request $request, response $response)
    {
        $this->form->checkLogin($request, $response);
        if (isset($_SESSION['id']))
            return $response->withRedirect('/');
        return $this->view->render($response, 'templates/logForm/login.html.twig');
    }
    
    /**
     * @param $request requestInterface
     * @param $response responseInterface
     * @return twig view
     */
    public function logout (request $request, response $response)
    {
        session_destroy();
        return $response->withRedirect('/login');
    }
}
