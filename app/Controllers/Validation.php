<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Validation extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $get = $request->getParams();
        $account = $this->user->getUser($get['login']);
        if ($account && ($get['token'] === $account['token']))
        {
            $_SESSION['pseudo'] = $account['pseudo'];
            $_SESSION['id'] = $account['id'];
            $_SESSION['profil'] = $account;
            $_SESSION['profil']['token'] = password_hash(random_bytes(6), PASSWORD_DEFAULT);
            $user->updateToken($account['pseudo'], $_SESSION['profil']['token']);
            if ($get['action'] === 'reinit')
                return $response->withRedirect('/editPassword');
            elseif ($get['action'] === 'delete')
            {
                $this->deletAccount();
                return $response->withRedirect('/logout');
            }
            $this->user->activate();
        }
        return $response->withRedirect('/');
    }

    private function deleteAccount()
    {
        $id = $_SESSION['id'];
        $pseudo = $_SESSION['profil']['pseudo'];
        $mail = $_SESSION['profil']['mail'];
        session_unset();
        session_destroy();
        if ($this->user->deleteUser($id)
            && $this->friends->delAllFriends($id)
            && $this->msg->delAllMessages($id)
            && $this->tag->delAllUserTag($id)
            && $this->notif->deleteNotifications($id)
            && $this->blacklist->deleteBlacklist($id))
        {
            $this->mail->sendDeleteMail2($pseudo, $mail);
        }
    }
}
