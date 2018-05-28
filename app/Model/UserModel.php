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
        $req = $this->db->query('SELECT * FROM user LIMIT 40');
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
        $img = array(
            'Rick' => 'img/rick.png',
            'Morty' => 'img/morty.jpg',
            'Beth' => 'img/beth.jpg',
            'Jerry' => 'img/jerry.png',
            'Summer' => 'img/summer.jpg'
        );
        $req = $this->db->prepare('
                INSERT INTO user (pseudo, password, email, gender, activ, token, img)
                VALUES (?, ?, ?, ?, ?, ?, ?)');
        $req->execute(array(
                $post['pseudo'],
                $post['password'],
                $post['email'],
                $post['gender'],
                $post['activ'],
                $post['token'],
                $img[$post['gender']]
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
    
    public function updateUser($data)
    {
        $post = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        { 
            foreach ($_POST as $key => $value)
                $post[$key] = $value;
        }
        else
            $post = $data;
        $this->debug->ft_print($post);
        $req = $this->db->prepare('
            UPDATE user
            SET pseudo = ?,
                email = ?,
                forname = ?,
                name = ?,
                birthdate = ?,
                gender = ?,
                sexuality = ?,
                biography = ?,
                lattitude = ?,
                longitude = ?
            where pseudo = ?'
        );
        $req->execute(array(
                $post['pseudo'],
                $post['email'],
                $post['forname'],
                $post['name'],
                $post['birthdate'],
                $post['gender'],
                $post['sexuality'],
                $post['biography'],
                $post['lattitude'],
                $post['longitude'],
                $_SESSION['pseudo']
        ));
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
