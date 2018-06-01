<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * class PagesController
 * this class is called by each routes
 */
class HomeController extends \App\Constructor
{
    /**
     * @param request $request requestInterface
     * @param $response responseInterface
     * @return twig view
     */
    public function home (request $request, response $response)
    {
        var_dump($_SESSION['profil']);
        return $this->view->render(
            $response,
            'templates/home/profil.html.twig',
            [
                'profil' => $_SESSION['profil'],
                'user' => $_SESSION,
                'friendReq' => $this->friends->getFriendsReqs($_SESSION['id']),
                'friends' => $this->friends->getFriends($_SESSION['id']),
                'tags' => $this->tag->getUserTags($_SESSION['id'])
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
                'templates/home/profil.html.twig',
                [
                    'profil' => $user,
                    'user' => $_SESSION,
                    'tags' => $this->tag->getUserTags($user['id'])
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
                'me' => $_SESSION['profil'],
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
            {
                $this->user->updateUser();
                $this->flash->addMessage('success', 'profil updated!');
            }
        }
        return $this->view->render(
            $response,
            'templates/home/editProfil.html.twig',
            [
                'profil' => $_SESSION['profil'],
                'characters' => $this->characters,
                'sexualPattern' => $this->sexualPattern,
                'flash' => $this->flash->getMessages(),
                'post' => $_POST
            ]
        );
    }

    /**
     * @param request $request requestInterface
     * @param $response responseInterface
     * @return twig view
     */
    public function editPassword (request $request, response $response)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->form->check($request))
        {
            if ($_POST['password'] === $_POST['password1'])
            {
                $this->user->updatePassUser();
                $this->flash->addMessage('success', 'password updated!');
            }
            else
                $this->flash->addMessage('fail', 'passwords doesnt match');
        }
        return $this->view->render(
            $response,
            'templates/home/editPassword.html.twig',
            [
                'flash' => $this->flash->getMessages(),
            ]
        );
    }
}
