<?php

namespace App\Model;

/**
 * class MessageModel
 * request to database about messages
 */
class MessageModel extends \App\Constructor
{
    /**
     * @param $hash array
     *
     * @return array
     */
    public function getMessages($hash)
    {
        $req = $this->db->prepare('SELECT owner, message FROM message WHERE id_user1 = ? AND id_user2 = ? ORDER BY date ASC LIMIT 100');
        $req->execute($hash);

        return $req->fetchAll();
    }

    /**
     * @param $hash array
     *
     * @return array
     */
    public function getAllMessages()
    {
        $req = $this->db->prepare('SELECT * FROM message WHERE id_user1 = ? OR id_user2 = ? ORDER BY date ASC');
        $req->execute([$_SESSION['id'], $_SESSION['id']]);

        return $req->fetchAll();
    }

    /**
     * @param $hash array
     */
    public function setMessage($hash)
    {
        $req = $this->db->prepare('INSERT INTO message VALUES (?, ?, ?, ?, ?)');
        $req->execute($hash);
    }

    public function delAllMessages($id)
    {
        $req = $this->db->prepare('DELETE FROM message WHERE id_user1 = ? OR id_user2 = ?');

        return $req->execute([$id, $id]);
    }
}
