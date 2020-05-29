<?php

namespace App\Model;

/**
 * class NotificationModel
 * request to database about notifications
 */
class BlacklistModel
{
    /** @var \PDO */
    private $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function getBlacklist(): array
    {
        $req = $this->db->prepare('SELECT * FROM blacklist WHERE iduser = ?');
        $req->execute([$_SESSION['id']]);

        return $req->fetchAll();
    }

    public function isBlacklistById(int $iduser, int $iduser_bl): bool
    {
        $req = $this->db->prepare('
SELECT 1 FROM blacklist
WHERE (iduser = ? AND iduser_bl = ?) OR (iduser = ? AND iduser_bl = ?)');
        $req->execute([$iduser, $iduser_bl, $iduser_bl, $iduser]);

        return !empty($req->fetch());
    }

    public function getAllBlacklist()
    {
        $req = $this->db->prepare('SELECT iduser, iduser_bl FROM blacklist WHERE iduser = ? OR iduser_bl = ?');
        $req->execute([$_SESSION['id'], $_SESSION['id']]);

        return $req->fetchAll();
    }

    public function setBlacklist(int $id): int
    {
        $req = $this->db->prepare('INSERT IGNORE INTO blacklist (iduser, iduser_bl) VALUES (?, ?)');
        $req->execute([$_SESSION['id'], $id]);

        return $req->rowCount();
    }

    public function deleteBlacklist(int $id): int
    {
        $req = $this->db->prepare('DELETE FROM blacklist WHERE iduser = ? OR iduser_bl = ?');
        $req->execute([$id, $id]);

        return $req->rowCount();
    }
}
