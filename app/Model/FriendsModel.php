<?php
namespace App\Model;

/**
 * class UserModel
 * request to database about user
 */
class FriendsModel extends \App\Constructor
{
    private function sortId($id1, $id2)
    {
        return (($id1 < $id2) ? [$id1, $id2] : [$id2, $id1]);
    }

    public function getFriend($id1, $id2)
    {
        $req = $this->db->prepare('SELECT * FROM friends WHERE id_user1 = ? AND id_user2 = ?');
        $req->execute($this->sortId($id1, $id2));
        return $req->fetch();
    }

    public function getFriends($id)
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
            SELECT *
            FROM friends
            INNER JOIN user
            ON friends.id_user1 = user.id
            WHERE id_user2 = ?
            ORDER BY user.pseudo
        ');
        $req1->execute(array($id));
        $req2->execute(array($id));
        return array_merge($req1->fetchAll(), $req2->fetchAll());
    }

    public function setFriend($id1, $id2)
    {
        $this->delFriendReq($id1, $id2);
        $this->delFriendReq($id2, $id1);
        $req = $this->db->prepare('INSERT INTO friends VALUES (?, ?)');
        $req->execute($this->sortId($id1, $id2));
    }

    public function delFriendReq($id1, $id2)
    {
        $req = $this->db->prepare('DELETE FROM friendsReq WHERE id_user1 = ? AND id_user2 = ?');
        $req->execute(array($id1, $id2));
    }

    public function delFriend($id1, $id2)
    {
        $req = $this->db->prepare('DELETE FROM friends WHERE id_user1 = ? AND id_user2 = ?');
        $req->execute($this->sortId($id1, $id2));
    }

    public function getFriendsReqs($id)
    {
        $req = $this->db->prepare('
            SELECT *
            FROM friendsReq
            INNER JOIN user
            ON friendsReq.id_user2 = user.id
            WHERE id_user1 = ?
            ORDER BY user.pseudo
        ');
        $req->execute(array($id));
        return $req->fetchAll();
    }

    public function getFriendReq($id1, $id2)
    {
        $req = $this->db->prepare('SELECT * FROM friendsReq WHERE id_user1 = ? AND id_user2 = ?');
        $req->execute(array($id1, $id2));
        return $req->fetch();
    }

    public function setFriendsReq($id1, $id2)
    {
        if (!($this->getFriendReq($id1, $id2)))
        {
            $req = $this->db->prepare('INSERT INTO friendsReq VALUE (?, ?)');
            $req->execute(array($id1, $id2));
            //return 'added!';
        }
        //else
            //return'already added';
    }
}

