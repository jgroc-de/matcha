<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class DeletePicture extends \App\Constructor
{
    public function route(Request $request, Response $response, array $args)
    {
        if ($url = $this->user->delPicture($args['id']))
        {
            if (strncmp('/img/', $url, 5))
            {
                echo $url;
                //unlink($url);
            }
            $_SESSION['profil']['img' . $args['id']] = '';
        }
        return $response;
    }
}
