<?php

namespace App\Controllers;

use App\Lib\FlashMessage;
use App\Lib\FormChecker;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class Contact
{
    const template = 'templates/logForm/contact.html.twig';

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
        $user = [];
        if (isset($_SESSION['profil'])) {
            $user = $_SESSION['profil'];
        }

        $data = array_merge([
            'user' => $user,
            'flash' => $this->flash->getMessages(),
            'PUB_CAPTCHA_KEY' => $_ENV['PUB_CAPTCHA_KEY']
        ]);

        return $this->view->render(
            $response,
            self::template,
            $data
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
