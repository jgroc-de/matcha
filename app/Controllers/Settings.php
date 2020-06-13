<?php

namespace App\Controllers;

use App\Lib\FlashMessage;
use App\Lib\FormChecker;
use App\Lib\MailSender;
use App\Lib\Validator;
use App\Model\NotificationModel;
use App\Model\UserModel;
use Memcached;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class Settings
{
    /** @var Twig */
    private $view;
    /** @var FlashMessage */
    private $flash;
    /** @var MailSender */
    private $mail;
    /** @var NotificationModel */
    private $notif;
    /** @var Validator */
    private $validator;
    /** @var UserModel */
    private $user;
    /** @var FormChecker */
    private $form;

    public function __construct(
        FlashMessage $flashMessage,
        FormChecker $formChecker,
        MailSender $mailSender,
        NotificationModel $notificationModel,
        UserModel $userModel,
        Validator $validator,
        Twig $view
    ) {
        $this->flash = $flashMessage;
        $this->form = $formChecker;
        $this->mail = $mailSender;
        $this->notif = $notificationModel;
        $this->user = $userModel;
        $this->validator = $validator;
        $this->view = $view;
    }

    public function rgpd(Request $request, Response $response, array $args)
    {
        return $this->renderSettings($response, ['rgpd' => true]);
    }

    public function editPassword(Request $request, Response $response, array $args): Response
    {
        return $this->renderSettings($response, ['editPwd' => true]);
    }

    public function mailPassword(Request $request, Response $response, array $args): Response
    {
        $get = $request->getParams();
        $account = $this->user->getUserById($_SESSION['id']);

        if (!$this->validator->validate($get, ['token']) || ($get['token'] !== $account['token']))
            return $response->withRedirect('/editPassword');

        $_SESSION['profil']['token'] = password_hash(random_bytes(6), PASSWORD_DEFAULT);
        $this->user->updateToken($account['pseudo'], $_SESSION['profil']['token']);
        return $this->renderSettings($response, ['editPwd' => true, 'reset' => true]);
    }

    public function updatePassword(Request $request, Response $response, array $args): Response
    {
        $this->form->checkPwd($request->getParsedBody());

        return $response->withJson($this->flash->getMessages());
    }

    public function editEmail(Request $request, Response $response, array $args): Response
    {
        return $this->renderSettings($response, ['editEmail' => true]);
    }

    public function updateEmail(Request $request, Response $response, array $args): Response
    {
        $post = $request->getParsedBody();
        if ($this->form->checkEmail($post)) {
            $this->user->updateEmail($post);
            $_SESSION['profil']['email'] = $post['email'];
            $this->mail->sendUpdateMail();
            $this->flash->addMessage('success', 'Email updated, check your mail !');
        }

        return $response->withJson($this->flash->getMessages());
    }

    public function editProfil(Request $request, Response $response, array $args): Response
    {
        return $this->renderSettings($response, ['editProfil' => true]);
    }

    public function updateProfil(Request $request, Response $response, array $args): Response
    {
        $post = $request->getParsedBody();
        if ($this->form->checkProfil($post) && $this->user->updateUser($post)) {
            $_SESSION['profil'] = array_replace($_SESSION['profil'], $post);
            $this->flash->addMessage('success', 'Profil updated !');
        }

        return $response->withJson($this->flash->getMessages());
    }

    private function renderSettings(Response $response, array $args): Response
    {
        $data = array_merge([
            'me' => $_SESSION['profil'],
            'flash' => $this->flash->getMessages(),
            'notification' => $this->notif->getNotification(),
        ], $args);
        $template = 'templates/in/editProfil.html.twig';

        return $this->view->render($response, $template, $data);
    }
}
