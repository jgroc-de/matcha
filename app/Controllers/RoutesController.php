<?php

namespace App\Controllers;

/**
 * class PagesController
 * this class is called by each routes
 */
class RoutesController extends ContainerClass
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
                'templates/signup.html.twig',
                [
                    'characters' => $this->characters
                ]
            );
        }
    }

    public function validation ($request, $response)
    {
        $get = $request->getParams();
        $account = $this->user->getUser($get['login']);
        if (!empty($account) && $get['token'] = $account['token'])
        {
            $_SESSION['pseudo'] = $account['pseudo'];
            $_SESSION['id'] = $account['id'];
            $this->user->activate();
        }
        return $response->withRedirect('/');
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
        return $this->view->render($response, 'templates/login.html.twig');
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

    /**
     * create database and tables
     *
     * @param $request requestInterface
     * @param $response responseInterface
     * @return twig view on login
     */
    public function setup ($request, $response)
    {
        $this->container->setup->init();
    }

    public function seed ($request, $response)
    {
        $this->container->setup->fakeFactory(400);
        $array = $this->container->user->getUsers();
        foreach ($array as $value)
            $this->container->debug->ft_print($value);
    }
}
