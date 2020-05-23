<?php

namespace App\Controllers;

use App\Lib\Common;
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

    public function __construct(UserModel $userModel, Common $common, MailSender $mail, Validator $validator)
    {
        $this->user = $userModel;
        $this->common = $common;
        $this->mail = $mail;
        $this->validator = $validator;
    }

    public function getAllDatas(Request $request, Response $response, array $args): Response
    {
        $this->common->sendAllDatas();
        $response->getBody()->write('Check your mailbox!');

        return $response;
    }

    public function deleteAccount(Request $request, Response $response, array $args): Response
    {
        if ($this->mail->sendDeleteMail()) {
            $msg = 'Check your mailbox!';
        } else {
            $msg = 'there is a bug… plz contact us, we will answer asap!';
        }
        $response->getBody()->write($msg);

        return $response;
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
