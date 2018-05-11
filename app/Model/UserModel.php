<?php
namespace App\Model;

/**
 * class UserModel
 * request to database about user
 */
class UserModel extends ContainerClass
{
    /**
     * @param $pseudo string
     * @return array
     */
    public function getUser($pseudo)
    {
        $req = $this->db->prepare('select * from user where pseudo = ?');
        $req->execute(array($pseudo));
        return $req->fetch();
    }

    public function setUser(array $post)
    {
        $req = $this->db->prepare('
                INSERT INTO user (pseudo, password, email, gender)
                VALUES (?, ?, ?, ?)');
        $req->execute(array(
                $post['pseudo'],
                $post['password'],
                $post['email'],$post['gender']));
    }

    /**
     * @param $id int
     * @return array
     */
    public function getUserById($id)
    {
        $req = $this->db->prepare('select * from user where id = ?');
        $req->execute(array($id));
        return $req->fetch();
    }
}
