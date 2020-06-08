<?php

namespace App\Controllers;

use App\Lib\Common;
use App\Lib\FlashMessage;
use App\Lib\MailSender;
use App\Lib\Validator;
use App\Model\UserModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RGPD
{
    /** @var UserModel */
    private $user;
    /** @var Common */
    private $common;
    /** @var MailSender */
    private $mail;
    /** @var Validator */
    private $validator;
    /** @var FlashMessage */
    private $flash;

    public function __construct(
        Common $common,
        FlashMessage $flashMessage,
        MailSender $mail,
        UserModel $userModel,
        Validator $validator
    ) {
        $this->common = $common;
        $this->flash = $flashMessage;
        $this->mail = $mail;
        $this->user = $userModel;
        $this->validator = $validator;
    }

    public function getAllData(Request $request, Response $response, array $args): Response
    {
        $this->common->sendAllData();

        return $response->withJson($this->flash->getMessages());
    }

    public function deleteAccount(Request $request, Response $response, array $args): Response
    {
        $this->mail->sendDeleteMail();

        return $response->withJson($this->flash->getMessages());
    }

    public function validationDeletion(Request $request, Response $response, array $args): Response
    {
        $get = $request->getParams();
        if (!$this->validator->validate($get, ['id', 'token', 'action'])) {
            return $response->withStatus(404);
        }
        $account = $this->user->getUserById($get['id']);
        if (empty($account) || ($get['token'] !== $account['token'])) {
            return $response->withRedirect('/');
        }
        $_SESSION['id'] = $account['id'];
        $_SESSION['profil'] = $account;
        $_SESSION['profil']['token'] = password_hash(random_bytes(6), PASSWORD_DEFAULT);
        $this->user->updateToken($account['pseudo'], $_SESSION['profil']['token']);
        if ($get['action'] === 'ini') {
            return $response->withRedirect('/editPassword');
        }
        if ($get['action'] === 'del') {
            $this->common->deleteAccountExecute();

            return $response->withRedirect('/logout');
        }
        $this->user->activate();

        return $response->withRedirect('/');
    }
}
