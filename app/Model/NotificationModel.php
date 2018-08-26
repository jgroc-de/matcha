<?php
namespace App\Model;

/**
 * class NotificationModel
 * request to database about notifications
 */
class NotificationModel extends \App\Constructor
{
    /**
     * @param $hash array
     * @return array
     */
    public function getNotification()
    {
        $req = $this->db->prepare('SELECT * FROM notification WHERE iduser = ? ORDER BY date DESC LIMIT 10');
        $req->execute(array($_SESSION['id']));
        return $req->fetchAll();
    }
    
    /**
     * @param $hash array
     */
    public function setNotification($hash)
    {
        $req = $this->db->prepare('INSERT INTO notification (iduser, link, message, date) VALUES (?, ?, ?,"' . date('Y-m-d H:i:s') . '")');
        $req->execute($hash);
    }
}
