<?php
namespace App\Model;

/**
 * class NotificationModel
 * request to database about notifications
 */
class BlacklistModel extends \App\Constructor
{
    /**
     * @param $hash array
     * @return array
     */
    public function getBlacklist()
    {
        $req = $this->db->prepare('SELECT * FROM blacklist WHERE iduser = ?');
        $req->execute(array($_SESSION['id']));
        return $req->fetchAll();
    }
    
    /**
     * @param $hash array
     * @return array
     */
    public function getBlacklistById($idu, $idb)
    {
        $req = $this->db->prepare('SELECT * FROM blacklist WHERE iduser = ? and iduser_bl = ?');
        $req->execute(array($idu, $idb));
        return $req->fetch();
    }

    /**
     * @param $hash array
     * @return array
     */
    public function getAllBlacklist()
    {
        $req = $this->db->prepare('SELECT iduser, iduser_bl FROM blacklist WHERE iduser = ? OR iduser_bl = ?');
        $req->execute(array($_SESSION['id'], $_SESSION['id']));
        return $req->fetchAll();
    }
    
    /**
     * @param $hash array
     */
    public function setBlacklist($id)
    {
        $req = $this->db->prepare('INSERT INTO blacklist (iduser, iduser_bl) VALUES (?, ?)');
        $req->execute(array($_SESSION['id'], $id));
    }
    
    public function deleteBlacklist($id)
    {
        $req = $this->db->prepare('DELETE FROM blacklist WHERE iduser = ? OR iduser_bl = ?');
        return $req->execute(array($id, $id));
    }
}
