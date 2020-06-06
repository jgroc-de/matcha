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
        $req = $this->db->prepare('SELECT * FROM usertags WHERE idtag = ? AND id_user = ?');
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
            AND id_user = ?
        ');
        $req->execute([$tag, $user]);

        return $req->fetch();
    }

    public function getCommonUserIdTags(array $usersID, array $tagsID): array
    {
        $tags = [];
        foreach($tagsID as $tag) {
            $tags[] = $tag['id'];
        }
        $tags = implode(',', $tags);
        $users = [];
        foreach($usersID as $user) {
            $users[] = $user['id'];
        }
        $users = implode(',', $users);
        $req = $this->db->prepare("
            SELECT hashtags.id, usertags.id_user
            FROM hashtags
            INNER JOIN usertags
            ON usertags.idtag = hashtags.id
            WHERE usertags.id_user IN ($users)
                AND hashtags.id IN ($tags)
        ");
        $req->execute();

        return $req->fetchAll();
    }

    public function getUserTags(int $userID): array
    {
        $req = $this->db->prepare('
            SELECT hashtags.id, tag
            FROM usertags
            INNER JOIN hashtags
            ON usertags.idtag = hashtags.id
            WHERE id_user = ?
            ORDER BY hashtags.tag
        ');
        $req->execute([$userID]);

        return $req->fetchAll();
    }

    public function getAllUserTags(int $userID): array
    {
        $req = $this->db->prepare('
            SELECt tag
            FROM usertags
            INNER JOIN hashtags
            ON usertags.idtag = hashtags.id
            WHERE id_user = ?
            ORDER BY hashtags.tag
        ');
        $req->execute([$userID]);

        return $req->fetchAll();
    }

    public function setTag(string $tag): bool
    {
        $req = $this->db->prepare('INSERT INTO hashtags (tag) VALUES (?)');

        try {
            return $req->execute([$tag]);
        } catch (\PDOException $error) {
            return false;
        }
    }

    public function setUserTag(int $idtag): bool
    {
        $req = $this->db->prepare('INSERT INTO usertags (idtag, id_user) VALUES (?,?)');

        try {
            return $req->execute([$idtag, $_SESSION['id']]);
        } catch (\PDOException $error) {
            return false;
        }
    }

    public function delUserTag(int $idTag, int $iduser): bool
    {
        $req = $this->db->prepare('DELETE FROM usertags WHERE idtag = ? AND id_user = ?');

        return $req->execute([$idTag, $idUser]);
    }

    public function delAllUserTag(int $idUser): bool
    {
        $req = $this->db->prepare('DELETE FROM usertags WHERE id_user = ?');

        return $req->execute([$idUser]);
    }
}
