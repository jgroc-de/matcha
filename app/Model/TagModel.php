<?php

namespace App\Model;

class TagModel
{
    /** @var \PDO */
    private $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * @return array|bool
     */
    public function getTag(string $tag)
    {
        $req = $this->db->prepare('SELECT * FROM hashtags WHERE tag = ?');
        $req->execute([$tag]);

        return $req->fetch();
    }

    /**
     * @return array|bool
     */
    public function getUserTag(int $id, string $user)
    {
        $req = $this->db->prepare('SELECT * FROM usertags WHERE idtag = ? AND iduser = ?');
        $req->execute([$id, $user]);

        return $req->fetch();
    }

    /**
     * @return array|bool
     */
    public function getUserTagByName(string $tag, int $user)
    {
        $req = $this->db->prepare('
            SELECT *
            FROM usertags
            INNER JOIN hashtags
            ON hashtags.id = usertags.idtag
            WHERE hashtags.tag = ?
            AND iduser = ?
        ');
        $req->execute([$tag, $user]);

        return $req->fetch();
    }

    public function getUserTags(int $userId): array
    {
        $req = $this->db->prepare('
            SELECT hashtags.id, tag
            FROM usertags
            INNER JOIN hashtags
            ON usertags.idtag = hashtags.id
            WHERE iduser = ?
            ORDER BY hashtags.tag
        ');
        $req->execute([$userId]);

        return $req->fetchAll();
    }

    public function getAllUserTags(int $userId): array
    {
        $req = $this->db->prepare('
            SELECt tag
            FROM usertags
            INNER JOIN hashtags
            ON usertags.idtag = hashtags.id
            WHERE iduser = ?
            ORDER BY hashtags.tag
        ');
        $req->execute([$userId]);

        return $req->fetchAll();
    }

    public function setTag(string $tag): bool
    {
        $req = $this->db->prepare('INSERT INTO hashtags (tag) VALUES (?)');

        return $req->execute([$tag]);
    }

    public function setUserTag(int $idtag): bool
    {
        $req = $this->db->prepare('INSERT INTO usertags (idtag, iduser) VALUES (?,?)');

        return $req->execute([$idtag, $_SESSION['id']]);
    }

    public function delUserTag(int $idTag, int $idUser): bool
    {
        $req = $this->db->prepare('DELETE FROM usertags WHERE idtag = ? AND iduser = ?');

        return $req->execute([$idTag, $idUser]);
    }

    public function delAllUserTag(int $idUser): bool
    {
        $req = $this->db->prepare('DELETE FROM usertags WHERE iduser = ?');

        return $req->execute([$idUser]);
    }
}
