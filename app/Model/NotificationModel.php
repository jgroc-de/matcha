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
     *
     * @return array
     */
    public function getNotification()
    {
        $req = $this->db->prepare('SELECT * FROM notification WHERE dest = ? ORDER BY date DESC LIMIT 10');
        $req->execute([$_SESSION['id']]);

        return $req->fetchAll();
    }

    /**
     * @param $hash array
     *
     * @return array
     */
    public function getAllNotification()
    {
        $req = $this->db->prepare('SELECT message, date FROM notification WHERE dest = ? OR exp = ? ORDER BY date DESC');
        $req->execute([$_SESSION['id'], $_SESSION['id']]);

        return $req->fetchAll();
    }

    /**
     * @param $hash array
     */
    public function setNotification($hash)
    {
        $req = $this->db->prepare('INSERT INTO notification (dest, exp, link, message, date) VALUES (?, ?, ?, ?,"' . date('Y-m-d H:i:s') . '")');
        $req->execute($hash);
    }

    public function deleteNotifications($id)
    {
        $req = $this->db->prepare('DELETE FROM notification WHERE dest = ? OR exp = ?');

        return $req->execute([$id, $id]);
    }
}
