<?php

namespace App\Middlewares;

use App\Model\UserModel;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class authMiddleware
{
    /** @var UserModel */
    private $user;

    public function __construct(ContainerInterface $container)
    {
        $this->user = $container->get('user');
    }

    /**
     * middleware that redirect to '/login' if client is not login
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        if (!isset($_SESSION['id'])) {
            return $response->withRedirect('/login');
        }

        $_SESSION['profil'] = $this->user->getUserById($_SESSION['id']);
        if (empty($_SESSION['profil'])) {
            session_unset();
            session_destroy();

            return $response->withRedirect('/login');
        }

        return $next($request, $response);
    }
}
