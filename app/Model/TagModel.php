<?php

namespace App\Model;

class TagModel extends \App\Constructor
{
    public function setTag($tag)
    {
        $req = $this->db->prepare('INSERT INTO hashtags (tag) VALUES (?)');
        return $req->execute(array($tag));
    }

    public function getTag($tag)
    {
        $req = $this->db->prepare('SELECT * FROM hashtags WHERE tag = ?');
        $req->execute(array($tag));
        return $req->fetch();
    }

    public function setUserTag($tag)
    {
        if (!$this->getTag($tag))
            $this->setTag($tag);
        $tagInfo = $this->getTag($tag);
        if (!$this->getUserTag($tagInfo['id'], $_SESSION['id']))
        {
            $req = $this->db->prepare('INSERT INTO usertags (idtag, iduser) VALUES (?,?)');
            return $req->execute(array($tagInfo['id'], $_SESSION['id']));    
        }
        return false;
    }

    public function getUserTag($tag, $user)
    {
        $req = $this->db->prepare('SELECT * FROM usertags WHERE idtag = ? AND iduser = ?');
        $req->execute(array($tag, $user));
        return $req->fetch();
    }

    public function getUserTags($userId)
    {
        $req = $this->db->prepare('
            SELECT *
            FROM usertags
            INNER JOIN hashtags
            ON usertags.idtag = hashtags.id
            WHERE iduser = ?
        ');
        $req->execute(array($userId));
        return $req->fetchAll();
    }

    public function delUserTag($idTag, $idUser)
    {
        $req = $this->db->prepare('DELETE FROM usertags WHERE idtag = ? AND iduser = ?');
        return $req->execute(array($idTag, $idUser));
    }
}
