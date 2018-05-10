<?php

namespace App\Controllers;

/**
 * class PagesController
 * this class is called by each routes
 */
class PagesController
{
    /**
     * @var array : for $container
     */
    private $container;

    /**
     * @param $container array
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param $request requestInterface : route for home
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
     * @param $request requestInterface : route for signup
     * @param $response responseInterface
     * @return twig view
     */
    public function signup ($request, $response)
    {
        $characters = ['Rick', 'Morty', 'Beth', 'Jerry', 'Summer'];
        return $this->container->view->render($response, 'templates/sign.html.twig', ['characters' => $characters]);
    }

    /**
     * @param $request requestInterface : route for login
     * @param $response responseInterface
     * @return twig view
     */
    public function login ($request, $response)
    {
        $params = $request->getParams();
        $req = $this->container->db->prepare('SELECT * FROM lol WHERE name = ?');
        $req->execute(array($params['pseudo']));
        $name = $req->fetchAll();
        print_r($name);
        return $this->view->render($response, $dir = 'templates/login.html.twig');
    }

    /**
     * @param $name string : shortcut to access dependencies in $container
     * @return $container['$name'] : matching class from container if any
     */
    public function __get($name)
    {
        return $this->container->get($name);
    }
}
