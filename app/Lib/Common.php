<?php

namespace App\Lib;

use App\Model\BlacklistModel;
use App\Model\FriendsModel;
use App\Model\MessageModel;
use App\Model\NotificationModel;
use App\Model\TagModel;
use App\Model\UserModel;

class Common
{
    /*
    private $blacklist;
    private $friends;
    private $msg;
    private $notif;
    private $tag;
    private $user;
    private $mail;

    public function __construct(
        BlacklistModel $blacklistModel,
        FriendsModel $friendsModel,
        MessageModel $messageModel,
        NotificationModel $notificationModel,
        TagModel $tagModel,
        UserModel $userModel,
        MailSender $mailSender
    ) {
        $this->blacklist = $blacklistModel;
        $this->friends = $friendsModel;
        $this->msg = $messageModel;
        $this->notif = $notificationModel;
        $this->tag = $tagModel;
        $this->user = $userModel;
        $this->mail = $mailSender;
    }
    */
    private $container;

    public function __construct(
        $container
    ) {
        $this->container = $container;
    }

    public function __get($name)
    {
        return $this->container->get($name);
    }

    public function sendAllDatas()
    {
        $data = [];
        $data['tag'] = $this->tag->getAllUserTags($_SESSION['id']);
        $data['message'] = $this->msg->getAllMessages();
        $data['notif'] = $this->notif->getAllNotification();
        $data['friends'] = $this->friends->getAllFriends($_SESSION['id']);
        $data['friendsReq'] = $this->friends->getAllFriendsReqs();
        $data['blacklist'] = $this->blacklist->getAllBlacklist();
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $data[$key] = $this->implodeData($value);
            } else {
                $data[$key] = ['empty'];
            }
        }
        $data = array_merge($data, $this->splitImg($this->user->getAllDatas()));
        $this->mail->sendDataMail($data);
    }

    private function implodeData($data)
    {
        $str = [];
        foreach ($data as $value) {
            $str[] = implode(' - ', $value);
        }

        return $str;
    }

    private function splitImg($data)
    {
        $tab = [];
        $img = [];
        foreach ($data as $key => $value) {
            if (!strncmp($value, '/user_img', 9)) {
                $img[] = $value;
                unset($data[$key]);
            } elseif (!strncmp($key, 'img', 3)) {
                unset($data[$key]);
            }
        }
        $tab['user'] = $data;
        $tab['img'] = $img;

        return $tab;
    }

    private function deleteAccountExecute()
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
