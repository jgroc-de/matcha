<?php
namespace App\Model;

/**
 * class UserModel
 * request to database about user
 */
class FriendsModel extends \App\Constructor
{
    public function getFriend($id1, $id2)
    {
        $req = $this->db->prepare('SELECT * FROM friends WHERE id_user1 = ? AND id_user2 = ?');
        $req->execute($this->sortId($id1, $id2));
        return $req->fetch();
    }

    public function getFriends($id)
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
        $req1->execute(array($id));
        $req2->execute(array($id));
        return array_merge($req1->fetchAll(), $req2->fetchAll());
    }

    public function getAllFriends($id)
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
        $req1->execute(array($id));
        $req2->execute(array($id));
        return array_merge($req1->fetchAll(), $req2->fetchAll());
    }

    public function getFriendsReqs($id)
    {
        $req = $this->db->prepare('
            SELECT *
            FROM friendsReq
            INNER JOIN user
            ON friendsReq.id_user1 = user.id
            WHERE id_user2 = ?
            ORDER BY user.pseudo
        ');
        $req->execute(array($id));
        return $req->fetchAll();
    }

    public function getFriendReqs($id)
    {
        $req = $this->db->prepare('
            SELECT *
            FROM friendsReq
            INNER JOIN user
            ON friendsReq.id_user1 = user.id
            WHERE id_user2 = ? OR id_user1 = ?
            ORDER BY user.id
        ');
        $req->execute(array($id, $id));
        return $req->fetchAll();
    }

    public function getAllFriendsReqs()
    {
        $req = $this->db->prepare('
            SELECT id_user1, id_user2 
            FROM friendsReq
            WHERE id_user1 = ? OR id_user2 = ?
        ');
        $req->execute(array($_SESSION['id'], $_SESSION['id']));
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
            $user = $this->user->getUserById($id2);
            if ($this->getFriendReq($id2, $id1) or $user['bot'])
            {
                $this->setFriend($id1, $id2);
                $this->user->updatePopularity(5, $user);
                $this->user->updatePopularity(5, $_SESSION['profil']);
                $msg = array(
                    'category' => '"' . $user['publicToken'] . '"',
                    'dest' => $user['id'],
                    'exp' => $_SESSION['id'],
                    'link' => "/profil/" . $id1,
                    'msg' => "It's a match! say hi to " . $_SESSION['profil']['pseudo']
                );
                $this->MyZmq->send($msg);
                $msg = array(
                    'category' => '"' . $_SESSION['profil']['publicToken'] . '"',
                    'dest' => $_SESSION['id'],
                    'exp' => $user['id'],
                    'link' => "/profil/" . $id1,
                    'msg' => "It's a match! say hi to " . $user['pseudo']
                );
                $this->MyZmq->send($msg);
            }
            else
            {
                $req = $this->db->prepare('INSERT INTO friendsReq VALUE (?, ?, ?)');
                $req->execute(array($id1, $id2, true));
                $this->user->updatePopularity(1, $user);
                $msg = array(
                    'category' => '"' . $user['publicToken'] . '"',
                    'dest' => $user['id'],
                    'exp' => $_SESSION['id'],
                    'link' => "/profil/" . $id1,
                    'msg' => $_SESSION['profil']['pseudo'] . ' sent you a friend request'
                );
                $this->MyZmq->send($msg);
            }
        }
    }

    public function setFriend($id1, $id2)
    {
        $tab = $this->sortId($id1, $id2);
        $req = $this->db->prepare('
            SELECT * FROM friends
            WHERE id_user1 = ? AND id_user2 = ?');
        $req->execute($tab);
        if (empty($req->fetch()))
        {
            $this->eraseFriendReq($id1, $id2);
            $this->eraseFriendReq($id2, $id1);
            $req = $this->db->prepare('INSERT INTO friends VALUES (?, ?, ?)');
            $token = password_hash($id1 . random_bytes(4) . $id2, PASSWORD_DEFAULT);
            $this->flash->addMessage('success', 'this user is now your friends');
            $tab[] = $token;
            $req->execute($tab);
        }
        else
        {
            $this->flash->addMessage('success', 'this user is already your friends');
        }
    }

    public function delAllFriends($id)
    {
        $this->delAllFriendReq($id);
        $req = $this->db->prepare('DELETE FROM friends WHERE id_user1 = ? OR id_user2 = ?');
        $req->execute(array($id, $id));
    }

    public function delFriendReq($id1, $id2)
    {
        $req = $this->db->prepare('UPDATE friendsReq set visible = false WHERE id_user1 = ? AND id_user2 = ?');
        $req->execute(array($id1, $id2));
        $req = $this->db->prepare('UPDATE friendsReq set visible = false WHERE id_user1 = ? AND id_user2 = ?');
        $req->execute(array($id2, $id1));
    }

    public function delAllFriendReq($id)
    {
        $req = $this->db->prepare('DELETE FROM friendsReq WHERE id_user1 = ? OR id_user2 = ?');
        $req->execute(array($id, $id));
    }

    public function eraseFriendReq($id1, $id2)
    {
        $req = $this->db->prepare('DELETE FROM friendsReq WHERE id_user1 = ? AND id_user2 = ?');
        $req->execute(array($id1, $id2));
    }

    public function delFriend($id1, $id2)
    {
        $req = $this->db->prepare('DELETE FROM friends WHERE id_user1 = ? AND id_user2 = ?');
        if ($req->execute($this->sortId($id1, $id2)))
        {
            $user = $this->user->getUserById($id2);
            $msg = array(
                'category' => '"' . $user['publicToken'] . '"',
                'dest' => $user['id'],
                'exp' => $_SESSION['id'],
                'link' => "/",
                'msg' => $_SESSION['profil']['pseudo'] . " has erased your friendship link"
            );
            $this->MyZmq->send($msg);
        }
    }

    public function isFriend(array $id)
    {
        $req = $this->db->prepare('select * from friends where id_user1 = ? and id_user2 = ?');
        $req->execute($id);
        return $req->fetch();
    }
    
    private function sortId($id1, $id2)
    {
        return (($id1 < $id2) ? [$id1, $id2] : [$id2, $id1]);
    }
}
