<?php

namespace App\Controllers;

/**
 * class PagesController
 * this class is called by each routes
 */
class RoutesController extends ContainerClass
{
    /**
     * @var array
     */
    protected $characters = ['Rick', 'Morty', 'Beth', 'Jerry', 'Summer'];

    /**
     * @var array
     */
    protected $sexualPattern = ['bi', 'homo', 'hetero'];

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

    public function profil ($request, $response)
    {
        return $this->view->render(
            $response,
            'templates/profil.html.twig',
            [
                'characters' => $this->characters,
                'sexualPattern' => $this->sexualPattern
            ]
        );
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
        $file = file_get_contents(__DIR__ . '/../../database/matcha.sql');
        $req = $this->db->exec($file);
        return $this->view->render($response, 'templates/login.html.twig');
    }
}
