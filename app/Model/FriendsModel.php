<?php

namespace App\Model;

use App\Lib\FlashMessage;
use App\Lib\MyZmq;

/**
 * class UserModel
 * request to database about user
 */
class FriendsModel
{
    /** @var \PDO */
    private $db;
    /** @var MyZmq */
    private $MyZMQ;
    /** @var FlashMessage */
    private $flashMessage;
    /** @var UserModel */
    private $userModel;

    public function __construct(\PDO $db, MyZmq $MyZMQ, FlashMessage $flashMessage, UserModel $userModel)
    {
        $this->db = $db;
        $this->MyZMQ = $MyZMQ;
        $this->flashMessage = $flashMessage;
        $this->userModel = $userModel;
    }

    /**
     * @return bool|array
     */
    public function getFriend(int $id1, int $id2)
    {
        $req = $this->db->prepare('SELECT * FROM friends WHERE id_user1 = ? AND id_user2 = ?');
        $req->execute($this->sortId($id1, $id2));

        return $req->fetch();
    }

    public function getFriends(int $id): array
    {
        $req1 = $this->db->prepare('
            SELECT *
            FROM friends
            INNER JOIN user
            ON friends.id_user2 = user.id
            WHERE id_user1 = ?
            ORDER BY user.pseudo
        ');
        $req2 = $this->db->prepare('
            SELECT *
            FROM user
            INNER JOIN friends
            ON friends.id_user1 = user.id
            WHERE id_user2 = ?
            ORDER BY user.pseudo
        ');
        $req1->execute([$id]);
        $req2->execute([$id]);

        return array_merge($req1->fetchAll(), $req2->fetchAll());
    }

    public function getAllFriends(int $id): array
    {
        $req1 = $this->db->prepare('
            SELECT id, pseudo
            FROM friends
            INNER JOIN user
            ON friends.id_user2 = user.id
            WHERE id_user1 = ?
            ORDER BY user.pseudo
        ');
        $req2 = $this->db->prepare('
            SELECT id, pseudo
            FROM user
            INNER JOIN friends
            ON friends.id_user1 = user.id
            WHERE id_user2 = ?
            ORDER BY user.pseudo
        ');
        $req1->execute([$id]);
        $req2->execute([$id]);

        return array_merge($req1->fetchAll(), $req2->fetchAll());
    }

    public function getFriendsReqs(int $id): array
    {
        $req = $this->db->prepare('
            SELECT *
            FROM friendsReq
            INNER JOIN user
            ON friendsReq.id_user1 = user.id
            WHERE id_user2 = ?
            ORDER BY user.pseudo
        ');
        $req->execute([$id]);

        return $req->fetchAll();
    }

    public function getFriendReqs(int $id): array
    {
        $req = $this->db->prepare('
            SELECT *
            FROM friendsReq
            INNER JOIN user
            ON friendsReq.id_user1 = user.id
            WHERE id_user2 = ? OR id_user1 = ?
            ORDER BY user.id
        ');
        $req->execute([$id, $id]);

        return $req->fetchAll();
    }

    public function getAllFriendsReqs(): array
    {
        $req = $this->db->prepare('
            SELECT id_user1, id_user2 
            FROM friendsReq
            WHERE id_user1 = ? OR id_user2 = ?
        ');
        $req->execute([$_SESSION['id'], $_SESSION['id']]);

        return $req->fetchAll();
    }

    /**
     * @return bool|array
     */
    public function getFriendReq(int $id1, int $id2)
    {
        $req = $this->db->prepare('SELECT * FROM friendsReq WHERE id_user1 = ? AND id_user2 = ?');
        $req->execute([$id1, $id2]);

        return $req->fetch();
    }

    public function setFriendsReq(int $id1, int $id2)
    {
        $user = $this->userModel->getUserById($id2);
        if ($this->getFriendReq($id2, $id1) or $user['bot']) {
            $this->setFriend($id1, $id2);
            $this->userModel->updatePopularity(5, $user);
            $this->userModel->updatePopularity(5, $_SESSION['profil']);
            $msg = [
                'category' => '"' . $user['publicToken'] . '"',
                'dest' => $user['id'],
                'exp' => $_SESSION['id'],
                'link' => '/profil/' . $id1,
                'msg' => "It's a match! say hi to " . $_SESSION['profil']['pseudo'],
            ];
            $this->MyZmq->send($msg);
            $msg = [
                'category' => '"' . $_SESSION['profil']['publicToken'] . '"',
                'dest' => $_SESSION['id'],
                'exp' => $user['id'],
                'link' => '/profil/' . $id1,
                'msg' => "It's a match! say hi to " . $user['pseudo'],
            ];
            $this->MyZmq->send($msg);
        } else {
            $req = $this->db->prepare('INSERT INTO friendsReq VALUE (?, ?, ?)');
            $req->execute([$id1, $id2, true]);
            $this->userModel->updatePopularity(1, $user);
            $msg = [
                'category' => '"' . $user['publicToken'] . '"',
                'dest' => $user['id'],
                'exp' => $_SESSION['id'],
                'link' => '/profil/' . $id1,
                'msg' => $_SESSION['profil']['pseudo'] . ' sent you a friend request',
            ];
            $this->MyZmq->send($msg);
        }
    }

    public function setFriend(int $id1, int $id2)
    {
        $tab = $this->sortId($id1, $id2);
        $req = $this->db->prepare('
            SELECT * FROM friends
            WHERE id_user1 = ? AND id_user2 = ?');
        $req->execute($tab);
        if (empty($req->fetch())) {
            $this->eraseFriendReq($id1, $id2);
            $this->eraseFriendReq($id2, $id1);
            $req = $this->db->prepare('INSERT INTO friends VALUES (?, ?, ?)');
            $token = password_hash($id1 . random_bytes(4) . $id2, PASSWORD_DEFAULT);
            $this->flashMessage->addMessage('success', 'this user is now your friends');
            $tab[] = $token;
            $req->execute($tab);
        } else {
            $this->flashMessage->addMessage('success', 'this user is already your friends');
        }
    }

    public function delAllFriends(int $id)
    {
        $this->delAllFriendReq($id);
        $req = $this->db->prepare('DELETE FROM friends WHERE id_user1 = ? OR id_user2 = ?');
        $req->execute([$id, $id]);
    }

    public function delFriendReq(int $id1, int $id2)
    {
        $req = $this->db->prepare('UPDATE friendsReq set visible = false WHERE id_user1 = ? AND id_user2 = ?');
        $req->execute([$id1, $id2]);
        $req = $this->db->prepare('UPDATE friendsReq set visible = false WHERE id_user1 = ? AND id_user2 = ?');
        $req->execute([$id2, $id1]);
    }

    public function delAllFriendReq(int $id)
    {
        $req = $this->db->prepare('DELETE FROM friendsReq WHERE id_user1 = ? OR id_user2 = ?');
        $req->execute([$id, $id]);
    }

    public function eraseFriendReq(int $id1, int $id2)
    {
        $req = $this->db->prepare('DELETE FROM friendsReq WHERE id_user1 = ? AND id_user2 = ?');
        $req->execute([$id1, $id2]);
    }

    public function delFriend(int $id1, int $id2)
    {
        $req = $this->db->prepare('DELETE FROM friends WHERE id_user1 = ? AND id_user2 = ?');
        if ($req->execute($this->sortId($id1, $id2))) {
            $user = $this->userModel->getUserById($id2);
            $msg = [
                'category' => '"' . $user['publicToken'] . '"',
                'dest' => $user['id'],
                'exp' => $_SESSION['id'],
                'link' => '/',
                'msg' => $_SESSION['profil']['pseudo'] . ' has erased your friendship link',
            ];
            $this->MyZmq->send($msg);
        }
    }

    /**
     * @return bool|array
     */
    public function isFriend(array $id)
    {
        $req = $this->db->prepare('select * from friends where id_user1 = ? and id_user2 = ?');
        $req->execute($id);

        return $req->fetch();
    }

    private function sortId(int $id1, int $id2): array
    {
        return ($id1 < $id2) ? [$id1, $id2] : [$id2, $id1];
    }
}
