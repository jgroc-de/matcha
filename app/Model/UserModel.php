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

    /**
     * @param $pseudo string
     * @return array
     */
    public function getUser(string $pseudo)
    {
        $req = $this->db->prepare('select * from user where pseudo = ?');
        $req->execute(array($pseudo));
        return $req->fetch();
    }

    /**
     * @param $id int
     * @return array
     */
    public function getUserById(int $id)
    {
        $req = $this->db->prepare('select * from user where id = ?');
        $req->execute(array($id));
        return $req->fetch();
    }
 
    /**
     * @param $email string email
     * @return array
     */
    public function getUserByEmail(string $email)
    {
        $req = $this->db->prepare('select * from user where email = ?');
        $req->execute(array($email));
        return $req->fetch();
    }
    
    public function getUsersBySexuality(array $age, float $delta_lng = 0.2, float $delta_lat = 0.2)
    {
        $reqTab = array();
        switch ($_SESSION['profil']['sexuality'])
        {
            case 'homo':
                $where = "AND gender = '" . $_SESSION['profil']['gender'] . "' AND sexuality <> 'hetero'";
                break;
            case 'hetero':
                $where = "AND gender <> '" . $_SESSION['profil']['gender'] . "' AND sexuality <> 'homo'";
                break;
            default:
                $where = '';
        }
        $req = $this->db->prepare(
            "SELECT pseudo, sexuality, biography, lattitude, longitude, img1, birthdate, gender, id, popularity
            FROM user 
            WHERE birthdate BETWEEN ? AND ? $where
            AND lattitude BETWEEN ? AND ?
            AND longitude BETWEEN ? AND ?
            ORDER BY popularity DESC
            LIMIT 250"
            );
        $req->execute(array(
            $age['max'],
            $age['min'],
            $_SESSION['profil']['lattitude'] - $delta_lat,
            $_SESSION['profil']['lattitude'] + $delta_lat,
            $_SESSION['profil']['longitude'] - $delta_lng,
            $_SESSION['profil']['longitude'] + $delta_lng
        ));
        return $req->fetchAll();
    }

    public function getUserByCriteria(array $age, array $target = array(), array $pop, int $dist)
    {
        $delta_lat = 5;
        $delta_lng = 5;
        $count = str_repeat('?,', count($target) - 1) . '?';
        $req = $this->db->prepare(
            "SELECT pseudo, sexuality, biography, lattitude, longitude, img1, birthdate, gender, id, popularity
            FROM user
            WHERE gender IN ($count)
            AND birthdate BETWEEN ? AND ?
            AND lattitude BETWEEN ? AND ?
            AND longitude BETWEEN ? AND ?
            AND popularity BETWEEN ? AND ?
            LIMIT 250"
        );
        $array = array_merge($target, [
            $age['max'],
            $age['min'],
            $_SESSION['profil']['lattitude'] - $delta_lat,
            $_SESSION['profil']['lattitude'] + $delta_lat,
            $_SESSION['profil']['longitude'] - $delta_lng,
            $_SESSION['profil']['longitude'] + $delta_lng,
            $pop['min'],
            $pop['max']]
        );
        $req->execute($array);
        return $req->fetchAll();
    }
    
    /**
     * @param $pseudo string
     * @return array
     */
    public function getUserByPseudo($pseudo)
    {
        $req = $this->db->prepare(
            'SELECT pseudo, sexuality, biography, lattitude, longitude, img1, birthdate, gender, id, popularity
            FROM user WHERE pseudo LIKE ? ORDER BY pseudo LIMIT 50');
        $req->execute(array($pseudo . '%'));
        return $req->fetchAll();
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
                INSERT INTO user (pseudo, password, name, surname, email, gender, token, publicToken, img1, lattitude, longitude)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $req->execute(array(
                $post['pseudo'],
                password_hash($post['password'], PASSWORD_DEFAULT),
                $post['name'],
                $post['surname'],
                $post['email'],
                $post['gender'],
                $post['token'],
                time() . $post['pseudo'] . bin2hex(random_bytes(4)),
                'img/' . $img[rand(0, 4)],
                $post['lat'],
                $post['lng']
            ));
    }

    public function updateFakeUser($post)
    {
        $req = $this->db->prepare(
            'UPDATE user
            SET pseudo = ?,
            email = ?,
            name = ?,
            surname = ?,
            birthdate = ?,
            gender = ?,
            sexuality = ?,
            biography = ?,
            lattitude = ?,
            longitude = ?,
            bot = ?,
            popularity = ?
            where pseudo = ?');
        $req->execute(array(
            $post['pseudo'],
            $post['email'],
            $post['name'],
            $post['surname'],
            $post['birthdate'],
            $post['gender'],
            $post['sexuality'],
            $post['biography'],
            $post['lat'],
            $post['lng'],
            true,
            $post['popularity'],
            $post['pseudo']
        ));
    }
    
    public function updateUser($post)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        { 
            $req = $this->db->prepare(
                'UPDATE user
                SET pseudo = ?,
                email = ?,
                name = ?,
                surname = ?,
                birthdate = ?,
                gender = ?,
                sexuality = ?,
                biography = ?
                where pseudo = ?');
            return $req->execute(array(
                $post['pseudo'],
                $post['email'],
                $post['name'],
                $post['surname'],
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

    public function updateGeolocation($lat, $lon, $id)
    {
        $req = $this->db->prepare('
            UPDATE user
            SET lattitude = ?, longitude = ?
            WHERE id = ?
        ');
        return $req->execute(array(floatval($lat), floatval($lon), $id));
    }

    public function activate()
    {
        $req = $this->db->prepare('UPDATE user SET activ = 1 WHERE id = ?');
        $req->execute(array($_SESSION['id']));
    }
    
    public function updatePublicToken()
    {
        $token = time() . $_SESSION['profil']['pseudo'] . bin2hex(random_bytes(4));
        $req = $this->db->prepare('UPDATE user SET publicToken = ? WHERE id = ?');
        $req->execute(array($token, $_SESSION['id']));
        $_SESSION['profil']['publicToken'] = $token;
    }
    
    public function updateToken($pseudo, $token)
    {
        $req = $this->db->prepare('UPDATE user SET token = ? WHERE pseudo = ?');
        $req->execute(array($token, $pseudo));
    }

    public function updatePopularity(int $add, array $profil)
    {
        $pop = intval($profil['popularity']);
        $pop +=  $add;
        if($pop > 100)
            $pop = 100;
        $req = $this->db->prepare('UPDATE user SET popularity = ? WHERE pseudo = ?');
        $req->execute(array($pop, $profil['pseudo']));
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
