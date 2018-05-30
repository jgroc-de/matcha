<?php
namespace App\Model;

/**
 * class UserModel
 * request to database about user
 */
class FriendsModel extends \App\Constructor
{
    protected function sortId($id1, $id2)
    {
        return (($id1 < $id2) ? [$id1, $id2] : [$id2, $id1]);
    }

    public function getFriend($id1, $id2)
    {
        $req = $this->db->prepare('SELECT * FROM friends WHERE id_user1 = ? AND id_user2 = ?');
        $req->execute($this->sortId($id1, $id2));
        return $req->fetch();
    }

    public function setFriend($id1, $id2)
    {
        $req = $this->db->prepare('iNSERT INTO friends VALUES (?, ?)');
        $req->execute($this->sortId($id1, $id2));
    }

    public function getFriendsReqs($id)
    {
        $req = $this->db->prepare('SELECT * FROM friendsReq WHERE id_user1 = ? OR id_user2 = ?');
        $req->execute(array($id, $id));
        return $req->fetch();
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
            return 'added!';
        }
        else
            return'already added';
    }
}

