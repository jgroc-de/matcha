<?php

namespace App\Controllers;

use App\Lib\FlashMessage;
use App\Lib\FormChecker;
use App\Lib\MailSender;
use App\Lib\Validator;
use App\Matcha;
use App\Model\NotificationModel;
use App\Model\UserModel;
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

    public function deleteAccount(Request $request, Response $response, array $args): Response
    {
        if ($this->mail->sendDeleteMail()) {
            $msg = 'Check your mailbox!';
        } else {
            $msg = 'there is a bug… plz contact us, we ill answer asap!';
        }
        $response->getBody()->write($msg);

        return $response;
    }

    public function editPassword(Request $request, Response $response, array $args): Response
    {
        return $this->renderSettings($response, ['editPwd' => true]);
    }

    public function postPassword(Request $request, Response $response, array $args): Response
    {
        $post = $request->getParsedBody();
        $this->form->checkPwd($post);

        return $this->editPassword($request, $response, $args);
    }

    public function editProfil(Request $request, Response $response, array $args): Response
    {
        return $this->renderSettings($response, ['editProfil' => true]);
    }

    public function postEditProfil(Request $request, Response $response, array $args): Response
    {
        $post = $request->getParsedBody();
        if ($this->form->checkProfil($post) && $this->user->updateUser($post)) {
            $_SESSION['profil'] = array_replace($_SESSION['profil'], $post);
            $this->flash->addMessage('success', 'profil updated!');
        } else {
            $this->flash->addMessage('failure', 'something went wrong');
        }

        return $this->editProfil($request, $response, $args);
    }

    private function renderSettings(Response $response, array $args): Response
    {
        $data = array_merge([
            'me' => $_SESSION['profil'],
            'characters' => Matcha::GENDER,
            'sexualPattern' => Matcha::KIND,
            'flash' => $this->flash->getMessages(),
            'year' => date('Y') - 18,
            'notification' => $this->notif->getNotification(),
        ], $args);

        return $this->view->render(
            $response,
            'templates/home/editProfil.html.twig',
            $data
        );
    }
}
