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
            return $this->view->render($response, 'templates/home.html.twig');
        else
            return $this->view->render($response, 'templates/login.html.twig');
    }
    
    /**
     * @param $request requestInterface
     * @param $response responseInterface
     * @return twig view
     */
    public function signup ($request, $response)
    {
        //$this->debug->ft_print($_SERVER);
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $this->form->checkSignup($request, $response);
            return $this->view->render(
                $response,
                'templates/login.html.twig'
            );
        }
        else
        {
            return $this->view->render(
                $response,
                'templates/signup.html.twig',
                ['characters' => $this->characters]
            );
        }
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
            return $this->view->render($response, 'templates/home.html.twig');
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
        return $this->view->render(
            $response,
            'templates/login.html.twig'
        );
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
            {
                $this->user->updateUser();
            }
        }
        else
        {
            $this->debug->ft_print($this->user->getUser($_SESSION['pseudo']));
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
            $this->debug->ft_print($_POST);
            if ($_POST['password'] === $_POST['password1'] && $this->form->check($request))
            {
                $this->user->updatePassUser();
            }
        }
        else
        {
            return $this->view->render($response, 'templates/password.html.twig');
        }
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
        $this->dbCreate;
        $file = file_get_contents(__DIR__ . '/../../database/matcha.sql');
        $req = $this->db->exec($file);
        $this->faker->fakeFactory(25);
        $array = $this->user->getUsers();
        foreach ($array as $value)
            $this->debug->ft_print($value);
        return $this->view->render($response, 'templates/login.html.twig');
    }
}
