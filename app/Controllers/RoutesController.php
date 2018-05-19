<?php

namespace App\Controllers;

/**
 * class PagesController
 * this class is called by each routes
 */
class RoutesController extends \App\Constructor
{
    /**
     * @param $request requestInterface
     * @param $response responseInterface
     * @return twig view
     */
    public function home ($request, $response)
    {
        if (isset($_SESSION['id']))
        {
            return $this->view->render(
                $response,
                'templates/home.html.twig',
                [
                    'name' => $_SESSION['pseudo']
                ]
            );
        }
        else
            return $response->withRedirect('/login');
    }
    
    /**
     * @param $request requestInterface
     * @param $response responseInterface
     * @return twig view
     */
    public function signup ($request, $response)
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
    public function validation ($request, $response)
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
    public function reInitPassword ($request, $response)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']))
        {
            if (!empty(($account = $this->user->getUserByEmail($_POST['email']))))
            {
                $account['token'] = password_hash(random_bytes(6), PASSWORD_DEFAULT);
                $this->user->updateToken($account['pseudo'], $account['token']);
                $this->mail->sendReInitMail($account['pseudo'], $account['email'], $account['token']);
                return $response->withRedirect('/');
            }
            var_dump('unknown mail');
        }
        else
            return $this->view->render($response, 'templates/logForm/reInitPassword.html.twig');
    }

    /**
     * @param $request requestInterface
     * @param $response responseInterface
     * @return twig view
     */
    public function login ($request, $response)
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
    public function logout ($request, $response)
    {
        session_destroy();
        return $response->withRedirect('/login');
    }

    /**
     * @param $request requestInterface
     * @param $response responseInterface
     * @return twig view
     */
    public function profil ($request, $response)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            if ($this->form->checkProfil($request))
                $this->user->updateUser();
        }
        else
        {
            return $this->view->render(
                $response,
                'templates/profil.html.twig',
                [
                    'profil' => $this->user->getUser($_SESSION['pseudo']),
                    'characters' => $this->characters,
                    'sexualPattern' => $this->sexualPattern,
                ]
            );
        }
    }

    /**
     * @param $request requestInterface
     * @param $response responseInterface
     * @return twig view
     */
    public function password ($request, $response)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            if ($_POST['password'] === $_POST['password1'] && $this->form->check($request))
                $this->user->updatePassUser();
        }
        else
            return $this->view->render($response, 'templates/password.html.twig');
    }
}
