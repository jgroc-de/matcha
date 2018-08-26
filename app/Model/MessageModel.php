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
     */
    public function setMessage($hash)
    {
        print_r($hash);
        $req = $this->db->prepare('INSERT INTO message VALUES (?, ?, ?, ?, ?)');
        $req->execute($hash);
    }
}
