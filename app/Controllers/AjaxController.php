<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class AjaxController extends \App\Constructor
{
    public function delUserTag (Request $request, Response $response, array $args)
    {
        if ($this->container->tag->delUserTag($args['id'], $_SESSION['id']))
            return $response;
        else
            return $response->withStatus(400);
    }

    public function addTag (Request $request, Response $response)
    {
        if ($this->container->tag->setUserTag($_POST['tag']))
        {
            $response->getBody()->write($_POST['tag']);
            return $response;
        }
        return $response->withStatus(400);
    }

    public function updateGeolocation (Request $request, Response $response, array $args)
    {
        $this->ft_geoIP->setLatLng();
        if ($this->user->updateGeolocation($_POST['lat'], $_POST['lng']))
        {
            $_SESSION['profil']['lattitude'] = $_POST['lat'];
            $_SESSION['profil']['longitude'] = $_POST['lng'];
            return $response;
        }
        return $response->withStatus(400);
    }

    public function friendRequest (Request $request, Response $response, array $args)
    {
        $response->getBody()->write(
            $this->container->friends->setFriendsReq(
                $_SESSION['id'],
                $args['id']
            ));
        return $response;
    }
    
    public function delFriendRequest (Request $request, Response $response, array $args)
    {
        $this->container->friends->delFriendReq(
            $_SESSION['id'],
            $args['id']
        );
        return $response;
    }

    public function delFriend (Request $request, Response $response, array $args)
    {
        $this->container->friends->delFriend(
            $_SESSION['id'],
            $args['id']
        );
        return $response;
    }

    public function tchat (Request $request, Response $response, array $args)
    {
        $friends = $this->container->friends;
        if ($friends->getFriend($_SESSION['id'], $args['id']))
        {
            $response->getBody()->write('yep');
        }
        else if ($friends->getFriendReq($_SESSION['id'], $args['id']) && $friends->getFriendReq($args['id'], $_SESSION['id']))
        {
            $friends->setFriend($_SESSION['id'], $args['id']);
            $this->chat($request, $response, $args);
        }
        else
            $response->getBody()->write('nop');
        return $response;
    }
}
