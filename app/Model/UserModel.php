<?php
namespace App\Model;

/**
 * class UserModel
 * request to database about user
 */
class UserModel extends \App\Constructor
{
    /**
     * @param $pseudo string
     * @return array
     */
    public function getUsers()
    {
        $req = $this->db->query('select * from user');
        return $req->fetchAll();
    }

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

    /**
     * @param $post array
     */
    public function setUser(array $post)
    {
        $req = $this->db->prepare('
                INSERT INTO user (pseudo, password, email, gender, activ, token)
                VALUES (?, ?, ?, ?, ?, ?)');
        $req->execute(array(
                $post['pseudo'],
                $post['password'],
                $post['email'],
                $post['gender'],
                $post['activ'],
                $post['token']
            ));
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
    
    /**
     * @param $email string email
     * @return array
     */
    public function getUserByEmail($email)
    {
        $req = $this->db->prepare('select * from user where email = ?');
        $req->execute(array($email));
        return $req->fetch();
    }
    
    public function updateUser()
    {
        $post = array();
        foreach ($_POST as $value)
            $post[] = $value;
        array_pop($post);
        $post[] = $_SESSION['id'];
        $req = $this->db->prepare('
            UPDATE user
            SET pseudo = ?, email = ?, forname = ?, name = ?, birthdate = ?, gender = ?, sexuality = ?, biography = ?
            where id = ?');
        $req->execute($post);
    }
    
    public function updatePassUser()
    {
        $post = array();
        $post[] = $_POST['password'];
        $post[] = $_SESSION['id'];
        $req = $this->db->prepare('
            UPDATE user
            SET password = ?
            where id = ?');
        $req->execute($post);
    }

    public function activate()
    {
        $req = $this->db->prepare('UPDATE user SET activ = 1 WHERE id = ?');
        $req->execute(array($_SESSION['id']));
    }

    public function updateToken($pseudo, $token)
    {
        $req = $this->db->prepare('UPDATE user SET token = ? WHERE pseudo = ?');
        $req->execute(array($token, $pseudo));
    }
}
