<?php

namespace App\Controllers;

class PagesController
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }
    
    public function home ($request, $response)
    {
        if (isset($_SESSION['id']))
            return $this->view->render($response, 'templates/home.html.twig');
        else
            return $this->view->render($response, $dir = 'templates/login.html.twig');
    }
    
    public function signup ($request, $response)
    {
        return $this->container->view->render($response, 'templates/sign.html.twig');
    }

    public function login ($request, $response)
    {
        $params = $request->getParams();
        $req = $this->container->db->prepare('SELECT * FROM lol WHERE name = ?');
        $req->execute(array($params['pseudo']));
        $name = $req->fetchAll();
        print_r($name);
        return $this->view->render($response, $dir = 'templates/login.html.twig');
    }

    public function __get($name)
    {
        return $this->container->get($name);
    }
}
