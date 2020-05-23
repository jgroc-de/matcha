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
    /** @var FlashMessage */
    private $flashMessage;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function isFriend(int $id1, int $id2): bool
    {
        $req = $this->db->prepare('SELECT 1 FROM friends WHERE id_user1 = ? AND id_user2 = ?');
        $req->execute($this->sortId($id1, $id2));

        return !empty($req->fetch());
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
     * @return array|bool
     */
    public function getFriendReq(int $id1, int $id2)
    {
        $req = $this->db->prepare('SELECT * FROM friendsReq WHERE id_user1 = ? AND id_user2 = ?');
        $req->execute([$id1, $id2]);

        return $req->fetch();
    }


    public function isLiked(int $id1, int $id2): bool
    {
        $req = $this->db->prepare('SELECT 1 FROM friendsReq WHERE id_user1 = ? AND id_user2 = ?');
        $req->execute([$id1, $id2]);

        return !empty($req->fetch());
    }

    public function setFriendsReq(int $id1, int $id2, array $user)
    {
        $req = $this->db->prepare('INSERT INTO friendsReq VALUE (?, ?, ?)');
        $req->execute([$id1, $id2, true]);
    }

    public function setFriend(int $id1, int $id2): bool
    {
        $tab = $this->sortId($id1, $id2);
        $tab[] = password_hash($id1 . random_bytes(4) . $id2, PASSWORD_DEFAULT);
        $req = $this->db->prepare('INSERT INTO friends VALUES (?, ?, ?)');
        if ($req->execute($tab)) {
            $this->delFriendReq($id1, $id2);
            return true;
        }

        return false;
    }

    public function delAllFriends(int $id)
    {
        $this->delAllFriendReq($id);
        $req = $this->db->prepare('DELETE FROM friends WHERE id_user1 = ? OR id_user2 = ?');
        $req->execute([$id, $id]);
    }

    public function delFriendReq(int $id1, int $id2)
    {
        $req = $this->db->prepare('DELETE FROM friendsReq WHERE id_user1 = ? AND id_user2 = ?');
        $req->execute([$id1, $id2]);
        $req = $this->db->prepare('DELETE FROM friendsReq WHERE id_user1 = ? AND id_user2 = ?');
        $req->execute([$id2, $id1]);
    }

    public function delAllFriendReq(int $id)
    {
        $req = $this->db->prepare('DELETE FROM friendsReq WHERE id_user1 = ? OR id_user2 = ?');
        $req->execute([$id, $id]);
    }

    public function delFriend(int $id1, int $id2): bool
    {
        $req = $this->db->prepare('DELETE FROM friends WHERE id_user1 = ? AND id_user2 = ?');

        return $req->execute($this->sortId($id1, $id2));
    }

    private function sortId(int $id1, int $id2): array
    {
        return ($id1 < $id2) ? [$id1, $id2] : [$id2, $id1];
    }
}
