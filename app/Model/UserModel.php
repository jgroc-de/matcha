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
     *
     * @return array
     */
    public function getUsers()
    {
        $req = $this->db->query('SELECT * FROM user LIMIT 500');

        return $req->fetchAll();
    }

    /**
     * @param $pseudo string
     *
     * @return array
     */
    public function getUser(string $pseudo)
    {
        $req = $this->db->prepare('select * from user where pseudo = ?');
        $req->execute([$pseudo]);

        return $req->fetch();
    }

    /**
     * @param $id int
     *
     * @return array
     */
    public function getUserById(int $id)
    {
        $req = $this->db->prepare('select * from user where id = ?');
        $req->execute([$id]);

        return $req->fetch();
    }

    /**
     * @param $email string email
     *
     * @return array
     */
    public function getUserByEmail(string $email)
    {
        $req = $this->db->prepare('select * from user where email = ?');
        $req->execute([$email]);

        return $req->fetch();
    }

    public function getUsersBySexuality(array $age, array $angle)
    {
        $reqTab = [];
        switch ($_SESSION['profil']['sexuality']) {
            case 'homo':
                $where = "AND gender = '" . $_SESSION['profil']['gender'] . "' AND sexuality <> 'hetero'";
                break;
            case 'hetero':
                $where = "AND gender <> '" . $_SESSION['profil']['gender'] . "' AND sexuality <> 'homo'";
                break;
            default:
                $where = '';
        }
        $id = $_SESSION['id'];
        $req = $this->db->prepare(
            "SELECT pseudo, sexuality, biography, lattitude, longitude, img1, birthdate, gender, id, popularity, lastlog
            FROM user 
            WHERE birthdate BETWEEN ? AND ? $where
            AND id <> $id
            AND lattitude BETWEEN ? AND ?
            AND longitude BETWEEN ? AND ?
            AND ACTIV = 1
            ORDER BY lastlog DESC
            LIMIT 200"
        );
        $req->execute([
            $age['max'],
            $age['min'],
            $_SESSION['profil']['lattitude'] - $angle['lat'],
            $_SESSION['profil']['lattitude'] + $angle['lat'],
            $_SESSION['profil']['longitude'] - $angle['lng'],
            $_SESSION['profil']['longitude'] + $angle['lng'],
        ]);

        return $req->fetchAll();
    }

    public function getUserByCriteria(array $age, array $target, array $pop, array $angle)
    {
        $id = $_SESSION['id'];
        $count = str_repeat('?,', count($target) - 1) . '?';
        $req = $this->db->prepare(
            "SELECT pseudo, sexuality, biography, lattitude, longitude, img1, birthdate, gender, id, popularity, lastlog
            FROM user
            WHERE gender IN ($count)
            AND id <> $id
            AND birthdate BETWEEN ? AND ?
            AND lattitude BETWEEN ? AND ?
            AND longitude BETWEEN ? AND ?
            AND popularity BETWEEN ? AND ?
            AND ACTIV = 1
            ORDER BY lastlog DESC
            LIMIT 200"
        );
        $array = array_merge(
            $target,
            [
                $age['max'],
                $age['min'],
                $_SESSION['profil']['lattitude'] - $angle['lat'],
                $_SESSION['profil']['lattitude'] + $angle['lat'],
                $_SESSION['profil']['longitude'] - $angle['lng'],
                $_SESSION['profil']['longitude'] + $angle['lng'],
                $pop['min'],
                $pop['max'], ]
        );
        $req->execute($array);

        return $req->fetchAll();
    }

    /**
     * @param $pseudo string
     *
     * @return array
     */
    public function getUserByPseudo($pseudo)
    {
        $id = $_SESSION['id'];
        $req = $this->db->prepare(
            "SELECT pseudo, sexuality, biography, lattitude, longitude, img1, birthdate, gender, id, popularity, lastlog
            FROM user
            WHERE pseudo LIKE ?
            AND id <> $id
            AND ACTIV = 1
            ORDER BY pseudo
            LIMIT 50"
        );
        $req->execute([$pseudo . '%']);

        return $req->fetchAll();
    }

    public function getAllDatas()
    {
        $req = $this->db->prepare(
            'SELECT pseudo, email, name, surname, biography, birthdate, lattitude, longitude, lastlog, gender, sexuality, img1, img2, img3, img4, img5
            FROM user
            WHERE id = ?'
        );
        $req->execute([$_SESSION['id']]);

        return $req->fetch();
    }

    /**
     * @param $post array
     */
    public function setUser(array $post)
    {
        $files = scandir('img');
        $img = [];
        foreach ($files as $file) {
            if (strpos($file, strtolower($post['gender'])) !== false) {
                $img[] = $file;
            }
        }
        $req = $this->db->prepare('
                INSERT INTO user (pseudo, password, name, surname, email, gender, token, publicToken, img1, lattitude, longitude, activ)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $req->execute([
            $post['pseudo'],
            $post['password'],
            $post['name'],
            $post['surname'],
            $post['email'],
            $post['gender'],
            $post['token'],
            time() . $post['pseudo'] . bin2hex(random_bytes(4)),
            'img/' . $img[rand(0, 4)],
            $post['lat'],
            $post['lng'],
            $post['activ'],
        ]);
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
            popularity = ?,
            lastlog = ?
            where pseudo = ?'
        );
        $req->execute([
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
            $post['lastlog'],
            $post['pseudo'],
        ]);
    }

    public function updateUser($post)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
                where pseudo = ?'
            );

            return $req->execute([
                $post['pseudo'],
                $post['email'],
                $post['name'],
                $post['surname'],
                $post['birthdate'],
                $post['gender'],
                $post['sexuality'],
                $post['biography'],
                $_SESSION['profil']['pseudo'],
            ]);
        }

        return false;
    }

    public function updatePassUser($pwd)
    {
        $req = $this->db->prepare('
            UPDATE user
            SET password = ?
            where id = ?');
        $req->execute([$pwd, $_SESSION['id']]);
    }

    public function updateGeolocation($lat, $lon, $id)
    {
        $req = $this->db->prepare('
            UPDATE user
            SET lattitude = ?, longitude = ?
            WHERE id = ?
        ');

        return $req->execute([floatval($lat), floatval($lon), $id]);
    }

    public function activate()
    {
        $req = $this->db->prepare('UPDATE user SET activ = 1 WHERE id = ?');
        $req->execute([$_SESSION['id']]);
    }

    public function updatePublicToken()
    {
        $token = time() . $_SESSION['profil']['pseudo'] . bin2hex(random_bytes(4));
        $req = $this->db->prepare('UPDATE user SET publicToken = ? WHERE id = ?');
        $req->execute([$token, $_SESSION['id']]);
        $_SESSION['profil']['publicToken'] = $token;
    }

    public function updateToken($pseudo, $token)
    {
        $req = $this->db->prepare('UPDATE user SET token = ? WHERE pseudo = ?');
        $req->execute([$token, $pseudo]);
    }

    public function updatePopularity(int $add, array $profil)
    {
        $pop = intval($profil['popularity']);
        $pop += $add;
        if ($pop > 100) {
            $pop = 100;
        }
        $req = $this->db->prepare('UPDATE user SET popularity = ? WHERE pseudo = ?');
        $req->execute([$pop, $profil['pseudo']]);
    }

    public function updateLastlog($id)
    {
        $req = $this->db->prepare('UPDATE user SET lastlog = ? WHERE id = ?');
        $req->execute([time(), $id]);
    }

    public function delPicture($nb)
    {
        $req = $this->db->prepare('UPDATE user SET ' . $nb . ' = NULL WHERE id = ?');

        return $req->execute([$_SESSION['id']]);
    }

    public function addPicture($nb, $path)
    {
        $req = $this->db->prepare('UPDATE user SET ' . $nb . ' = ? WHERE id = ?');

        return $req->execute([$path, $_SESSION['id']]);
    }

    public function deleteUser($id)
    {
        $req = $this->db->prepare('SELECT img1, img2, img3, img4, img5 FROM user WHERE id = ?');
        $req->execute([$id]);
        $url = $req->fetchAll();
        foreach ($url[0] as $key => $value) {
            if (!strncmp('/user_img/', $value, 10)) {
                unlink(__DIR__ . '/../../public' . $value);
            }
        }
        $req = $this->db->prepare('DELETE FROM user WHERE id = ?');

        return $req->execute([$id]);
    }
}
