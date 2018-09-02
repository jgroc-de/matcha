<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class DeletePicture extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        if ($this->user->delPicture($nb = 'img' . $args['id']))
        {
            if (strncmp('/img/', $_SESSION['profil'][$nb], 5))
            {
                unlink(ltrim($_SESSION['profil'][$nb], '/'));
            }
            $_SESSION['profil'][$nb] = '';
        }
        return $response;
    }
}
