<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class RGPD extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $this->getAllDatas();
        }
        return $this->view->render(
            $response,
            'templates/home/editProfil.html.twig',
            [
                'me' => $_SESSION['profil'],
                'characters' => $this->characters,
                'sexualPattern' => $this->sexualPattern,
                'flash' => $this->flash->getMessages(),
                'year' => date('Y') - 18,
                'notification' => $this->notif->getNotification(),
                'rgpd' => true
            ]
        );
    }

    public function deleteAccount(Request $request, Response $response, array $args)
    {
        if ($this->mail->sendDeleteMail())
            $this->flash->addMessage('success', 'Check your mailbox!');
        else
            $this->flash->addMessage('fail', 'there is a bug… plz contact us, we will anszer asap!');
        return $this->view->render(
            $response,
            'templates/home/editProfil.html.twig',
            [
                'me' => $_SESSION['profil'],
                'characters' => $this->characters,
                'sexualPattern' => $this->sexualPattern,
                'flash' => $this->flash->getMessages(),
                'year' => date('Y') - 18,
                'notification' => $this->notif->getNotification(),
                'rgpd' => true
            ]
        );
    }

    public function getAllDatas()
    {
        $user = $this->user->getAllDatas();
        if (empty($tag = $this->tag->getUserTags($_SESSION['id'])))
            $tag = array('You did not record any tags.');
        if (empty($message = $this->msg->getAllMessages()))
            $message = array('You did not send any message and no one message you.');
        if (empty($notif = $this->notif->getAllNotification()))
            $notif = array('We dont have any notification concerning your activity or your profil.');
        if (empty($friends = $this->friends->getFriends($_SESSION['id'])))
            $friends = array("You do not have friends. This can change!");
        if (empty($friendsReq = $this->friends->getAllFriendsReqs()))
            $friendsReq = array("You do not have friendsReq… for the moment.'");
        if (empty($blacklist = $this->blacklist->getAllBlacklist()))
            $blacklist = array("No one is on your blacklist and you are not on any blacklist. Congrats!");
        $data = array_merge($user, $tag, $message, $notif, $friends, $friendsReq, $blacklist);
        if ($this->mail->sendDataMail($data))
            $this->flash->addMessage('success', 'Check your mailbox!');
        else
            $this->flash->addMessage('fail', 'there is a bug… plz contact us!');
    }
}
