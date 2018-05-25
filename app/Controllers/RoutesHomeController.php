<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * class PagesController
 * this class is called by each routes
 */
class RoutesHomeController extends \App\Constructor
{
    /**
     * @param request $request requestInterface
     * @param $response responseInterface
     * @return twig view
     */
    public function home (request $request, response $response)
    {
        return $this->view->render(
            $response,
            'templates/home/home.html.twig',
            [
                'profil' => $this->user->getUser($_SESSION['pseudo']),
                'user' => $_SESSION
            ]
        );
    }

    /**
     * @param request $request requestInterface
     * @param $response responseInterface
     * @return twig view
     */
    public function profil (request $request, response $response, $args)
    {
        if(!empty($user = $this->user->getUserById($args['id'])))
        {
            return $this->view->render(
                $response,
                'templates/home/home.html.twig',
                [
                    'profil' => $user,
                    'user' => $_SESSION
                ]
            );
        }
    }

    /**
     * @param request $request requestInterface
     * @param $response responseInterface
     * @return twig view
     */
    public function search (request $request, response $response)
    {
        return $this->view->render(
            $response,
            'templates/home/search.html.twig',
            [
                'users' => $this->user->getUsers()
            ]
        );
    }

    /**
     * @param request $request requestInterface
     * @param $response responseInterface
     * @return twig view
     */
    public function editProfil (request $request, response $response)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            if ($this->form->checkProfil($request))
                $this->user->updateUser();
            return $response->withRedirect('/');
        }
        else
        {
            return $this->view->render(
                $response,
                'templates/home/profil.html.twig',
                [
                    'profil' => $this->user->getUser($_SESSION['pseudo']),
                    'characters' => $this->characters,
                    'sexualPattern' => $this->sexualPattern,
                ]
            );
        }
    }

    /**
     * @param request $request requestInterface
     * @param $response responseInterface
     * @return twig view
     */
    public function editPassword (request $request, response $response)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            if ($_POST['password'] === $_POST['password1'] && $this->form->check($request))
                $this->user->updatePassUser();
            return $response->withRedirect('/profil');
        }
        else
            return $this->view->render($response, 'templates/home/password.html.twig');
    }
}
