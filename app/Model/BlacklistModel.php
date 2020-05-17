<?php

namespace App\Model;

use App\Constructor;

/**
 * class NotificationModel
 * request to database about notifications
 */
class BlacklistModel extends Constructor
{
    public function getBlacklist(): array
    {
        $req = $this->db->prepare('SELECT * FROM blacklist WHERE iduser = ?');
        $req->execute([$_SESSION['id']]);

        return $req->fetchAll();
    }

    /**
     * @return bool|array
     */
    public function getBlacklistById(int $idu, int $idb)
    {
        $req = $this->db->prepare('SELECT * FROM blacklist WHERE iduser = ? and iduser_bl = ?');
        $req->execute([$idu, $idb]);

        return $req->fetch();
    }

    public function getAllBlacklist()
    {
        $req = $this->db->prepare('SELECT iduser, iduser_bl FROM blacklist WHERE iduser = ? OR iduser_bl = ?');
        $req->execute([$_SESSION['id'], $_SESSION['id']]);

        return $req->fetchAll();
    }

    public function setBlacklist(int $id)
    {
        $req = $this->db->prepare('INSERT INTO blacklist (iduser, iduser_bl) VALUES (?, ?)');
        $req->execute([$_SESSION['id'], $id]);
    }

    public function deleteBlacklist(int $id): bool
    {
        $req = $this->db->prepare('DELETE FROM blacklist WHERE iduser = ? OR iduser_bl = ?');

        return $req->execute([$id, $id]);
    }
}
