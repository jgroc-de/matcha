<?php

namespace App\Model;

class TagModel extends \App\Constructor
{
    public function getTag($tag)
    {
        $req = $this->db->prepare('SELECT * FROM hashtags WHERE tag = ?');
        $req->execute(array($tag));
        return $req->fetch();
    }

    public function getUserTag($id, $user)
    {
        $req = $this->db->prepare('SELECT * FROM usertags WHERE idtag = ? AND iduser = ?');
        $req->execute(array($id, $user));
        return $req->fetch();
    }

    public function getUserTagByName($tag, $user)
    {
        $req = $this->db->prepare('
            SELECT *
            FROM usertags
            INNER JOIN hashtags
            ON hashtags.id = usertags.idtag
            WHERE hashtags.tag = ?
            AND iduser = ?
        ');
        $req->execute(array($tag, $user));
        return $req->fetch();
    }

    public function getUserTags($userId)
    {
        $req = $this->db->prepare('
            SELECT hashtags.id, tag
            FROM usertags
            INNER JOIN hashtags
            ON usertags.idtag = hashtags.id
            WHERE iduser = ?
            ORDER BY hashtags.tag
        ');
        $req->execute(array($userId));
        return $req->fetchAll();
    }

    public function getAllUserTags($userId)
    {
        $req = $this->db->prepare('
            SELECt tag
            FROM usertags
            INNER JOIN hashtags
            ON usertags.idtag = hashtags.id
            WHERE iduser = ?
            ORDER BY hashtags.tag
        ');
        $req->execute(array($userId));
        return $req->fetchAll();
    }

    public function setTag($tag)
    {
        $req = $this->db->prepare('INSERT INTO hashtags (tag) VALUES (?)');
        return $req->execute(array($tag));
    }

    public function setUserTag($idtag)
    {
        $req = $this->db->prepare('INSERT INTO usertags (idtag, iduser) VALUES (?,?)');
        return $req->execute(array($idtag, $_SESSION['id']));    
    }

    public function delUserTag($idTag, $idUser)
    {
        $req = $this->db->prepare('DELETE FROM usertags WHERE idtag = ? AND iduser = ?');
        return $req->execute(array($idTag, $idUser));
    }

    public function delAllUserTag($idUser)
    {
        $req = $this->db->prepare('DELETE FROM usertags WHERE iduser = ?');
        return $req->execute(array($idUser));
    }
}
