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
            return $this->view->render($response, $dir = 'templates/login.html.twig');
    }
    
    /**
     * @param $request requestInterface
     * @param $response responseInterface
     * @return twig view
     */
    public function signup ($request, $response)
    {
        //a récup depuis la db
        $characters = ['Rick', 'Morty', 'Beth', 'Jerry', 'Summer'];
        return $this->container->view->render($response, 'templates/sign.html.twig', ['characters' => $characters]);
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

    public function logout ($request, $response)
    {
        session_destroy();
        return $this->view->render($response, $dir = 'templates/login.html.twig');
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
        return $this->view->render($response, $dir = 'templates/login.html.twig');
    }
}
