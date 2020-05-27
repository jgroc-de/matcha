<?php

namespace App\Controllers;

use App\Lib\FlashMessage;
use App\Lib\FormChecker;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class Contact
{
    const template = 'templates/contact.html.twig';

    /** @var Twig */
    private $view;
    /** @var FlashMessage */
    private $flash;
    /** @var FormChecker */
    private $form;

    public function __construct(Twig $view, FlashMessage $flashMessage, FormChecker $formChecker)
    {
        $this->view = $view;
        $this->flash = $flashMessage;
        $this->form = $formChecker;
    }

    public function page(Request $request, Response $response, array $args): Response
    {
        return $this->view->render(
            $response,
            self::template,
            [
                'me' => $_SESSION['profil'] ?? null,
                'flash' => $this->flash->getMessages(),
                'PUB_CAPTCHA_KEY' => $_ENV['PUB_CAPTCHA_KEY'],
            ]
        );
    }

    public function mail(Request $request, Response $response, array $args): Response
    {
        $post = $request->getParsedBody();
        /** @var FormChecker */
        $this->form->checkContact($post);

        return $this->page($request, $response, $args);
    }
}
