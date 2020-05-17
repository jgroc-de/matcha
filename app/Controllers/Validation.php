<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Validation extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $get = $request->getParams();
        if ($this->validator->validate($get, ['id', 'token', 'action']))
        {
            $account = $this->user->getUserById($get['id']);
            if (!empty($account) && ($get['token'] === $account['token']))
            {
                $_SESSION['id'] = $account['id'];
                $_SESSION['profil'] = $account;
                $_SESSION['profil']['token'] = password_hash(random_bytes(6), PASSWORD_DEFAULT);
                $this->user->updateToken($account['pseudo'], $_SESSION['profil']['token']);
                if ($get['action'] === 'ini')
                    return $response->withRedirect('/editPassword');
                elseif ($get['action'] === 'del')
                {
                    $this->deleteAccount();
                    return $response->withRedirect('/logout');
                }
                $this->user->activate();
            }
            return $response->withRedirect('/');
        }
        return $response->withStatus(400);
    }

    private function deleteAccount()
    {
        $id = $_SESSION['id'];
        $pseudo = $_SESSION['profil']['pseudo'];
        $mail = $_SESSION['profil']['email'];
        session_unset();
        session_destroy();
        $this->user->deleteUser($id);
        $this->friends->delAllFriends($id);
        $this->msg->delAllMessages($id);
        $this->tag->delAllUserTag($id);
        $this->notif->deleteNotifications($id);
        $this->blacklist->deleteBlacklist($id);
        $this->mail->sendDeleteMail2($pseudo, $mail);
    }
}
