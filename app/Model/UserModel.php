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
        $req = $this->db->query('SELECT * FROM user LIMIT 500');
        return $req->fetchAll();
    }

    public function getUsersBi($age_min, $age_max, $delta_lng = 0.2, $delta_lat = 0.2)
    {
        $req = $this->db->prepare(
            'SELECT pseudo, biography, lattitude, longitude, img1, birthdate, gender, id
            FROM user
            WHERE birthdate BETWEEN ? AND ?
            AND lattitude BETWEEN ? AND ?
            AND longitude BETWEEN ? AND ?
            ');
        $req->execute(array(
            $age_max,
            $age_min,
            $_SESSION['profil']['lattitude'] - $delta_lat,
            $_SESSION['profil']['lattitude'] + $delta_lat,
            $_SESSION['profil']['longitude'] - $delta_lng,
            $_SESSION['profil']['longitude'] + $delta_lng
        ));
        return $req->fetchAll();
    }

    public function getUsersHomo($age_min, $age_max, $gender, $delta_lng = 0.2, $delta_lat = 0.2)
    {
        $req = $this->db->prepare(
            'SELECT pseudo, biography, lattitude, longitude, img1, birthdate, gender, id
            FROM user
            WHERE gender = ?
            AND birthdate BETWEEN ? AND ?
            AND lattitude BETWEEN ? AND ?
            AND longitude BETWEEN ? AND ?'
        );
        $req->execute(array(
            $_SESSION['profil']['gender'],
            $age_max,
            $age_min,
            $_SESSION['profil']['lattitude'] - $delta_lat,
            $_SESSION['profil']['lattitude'] + $delta_lat,
            $_SESSION['profil']['longitude'] - $delta_lng,
            $_SESSION['profil']['longitude'] + $delta_lng
        ));
        return $req->fetchAll();
    }

    public function getUsersHetero($age_min, $age_max, $gender, $delta_lng = 0.2, $delta_lat =0.2)
    {
        $req = $this->db->prepare(
            'SELECT pseudo, biography, lattitude, longitude, img1, birthdate, gender, id
            FROM user
            WHERE gender <> ?
            AND birthdate BETWEEN ? AND ?
            AND lattitude BETWEEN ? AND ?
            AND longitude BETWEEN ? AND ?'
        );
        $req->execute(array(
            $_SESSION['profil']['gender'],
            $age_max,
            $age_min,
            $_SESSION['profil']['lattitude'] - $delta_lat,
            $_SESSION['profil']['lattitude'] + $delta_lat,
            $_SESSION['profil']['longitude'] - $delta_lng,
            $_SESSION['profil']['longitude'] + $delta_lng
        ));
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
        $files = scandir('img');
        $img = array();
        foreach ($files as $file)
        {
            if (strpos($file, strtolower($post['gender'])) !== false)
                $img[] = $file;
        }

        $req = $this->db->prepare('
                INSERT INTO user (pseudo, password, email, gender, token, img1, lattitude, longitude)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $req->execute(array(
                $post['pseudo'],
                password_hash($post['password'], PASSWORD_DEFAULT),
                $post['email'],
                $post['gender'],
                //$post['activ'],
                $post['token'],
                '/img/' . $img[rand(0, 4)],
                $post['lat'],
                $post['lng']
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
    
    public function getUserByCriteria($id)
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
    
    public function updateFakeUser($data)
    {
        $post = $data;
        $req = $this->db->prepare(
            'UPDATE user
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
            where pseudo = ?');
        $req->execute(array(
            $post['pseudo'],
            $post['email'],
            $post['forname'],
            $post['name'],
            $post['birthdate'],
            $post['gender'],
            $post['sexuality'],
            $post['biography'],
            $post['lat'],
            $post['lng'],
            $_SESSION['pseudo']
        ));
    }
    
    public function updateUser($post)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        { 
            $this->debug->ft_print($post);
            print_r($_SESSION);
            $req = $this->db->prepare(
                'UPDATE user
                SET pseudo = ?,
                email = ?,
                forname = ?,
                name = ?,
                birthdate = ?,
                gender = ?,
                sexuality = ?,
                biography = ?
                where pseudo = ?');
            return $req->execute(array(
                $post['pseudo'],
                $post['email'],
                $post['forname'],
                $post['name'],
                $post['birthdate'],
                $post['gender'],
                $post['sexuality'],
                $post['biography'],
                $_SESSION['profil']['pseudo']
            ));
        }
        return false;
    }
    
    public function updatePassUser()
    {
        $post = array();
        $post[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $post[] = $_SESSION['id'];
        $req = $this->db->prepare('
            UPDATE user
            SET password = ?
            where id = ?');
        $req->execute($post);
    }

    public function updateGeolocation($lat, $lon)
    {
        $req = $this->db->prepare('
            UPDATE user
            SET lattitude = ?, longitude = ?
            WHERE id = ?
        ');
        return $req->execute(array(floatval($lat), floatval($lon), $_SESSION['id']));
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

    public function updatePopularity(array $profil)
    {
        $req = $this->db->prepare('UPDATE user SET popularity = ? WHERE pseudo = ?');
        $req->execute(array($profil['popularity'], $profil['pseudo']));
    }

    public function delPicture($nb)
    {
        $nb = 'img' . $nb;
        $req = $this->db->prepare('SELECT ? FROM user WHERE id = ?');
        $req->execute(array($nb, $_SESSION['id']));
        $url = $req->fetch();
        $req = $this->db->prepare('UPDATE user SET ' . $nb . ' = NULL WHERE id = ?');
        $req->execute(array($_SESSION['id']));
        return $url[$nb];
    }

    public function addPicture($nb, $path)
    {
        $nb = 'img' . $nb;
        $req = $this->db->prepare('UPDATE user SET ' . $nb . ' = ? WHERE id = ?');
        return $req->execute(array('/user_img/' . $path, $_SESSION['id']));
    }
}
