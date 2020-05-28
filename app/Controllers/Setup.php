<?php

namespace App\Controllers;

use App\Lib\FormChecker;
use App\Matcha;
use App\Model\TagModel;
use App\Model\UserModel;
use Faker\Factory;
use Memcached;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Setup
{
    /** @var FormChecker */
    private $form;
    /** @var TagModel */
    private $tag;
    /** @var UserModel */
    private $user;
    /** @var array */
    private $settings;
    /** @var \PDO */
    private $db;

    public function __construct(
        FormChecker $form,
        TagModel $tagModel,
        UserModel $userModel,
        \PDO $db,
        array $dbSettings
    ) {
        $this->tag = $tagModel;
        $this->user = $userModel;
        $this->settings = $dbSettings;
        $this->db = $db;
        $this->form = $form;
        if (!is_dir(__DIR__ . '/../../public/user_img')) {
            mkdir(__DIR__ . '/../../public/user_img');
        }
    }

    public function memcached(Request $request, Response $response, array $args): Response
    {
        if ($_ENV['PROD']) {
            return $response->withRedirect('/');
        }
        if (class_exists('Memcached')) {
            $memcached = new Memcached();
            $memcached->addServer("127.0.0.1", 11211);
            $result = $memcached->get("Hey");

            if ($result) {
                echo $result;
            } else {
                echo "Pas de clé trouvée. J'en ajoute une!";
                $memcached->set("Hey", "New record in memcached!", 3600);
            }
        }

        return $response;
    }

    public function phpInfo(Request $request, Response $response, array $args): Response
    {
        if ($_ENV['PROD']) {
            return $response->withRedirect('/');
        }
        phpinfo();

        return $response;
    }

    public function initDB(Request $request, Response $response, array $args): Response
    {
        if ($_ENV['PROD']) {
            return $response->withRedirect('/');
        }
        $db = $this->settings;
        $pdo = new \PDO('mysql:host=' . $db['host'], $db['user'], $db['pass']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        $file = file_get_contents(__DIR__ . '/../../database/matcha.sql');
        $this->db->exec($file);

        return $response->withRedirect('/');
    }

    public function seed(Request $request, Response $response, array $args): Response
    {
        if ($_ENV['PROD']) {
            return $response->withRedirect('/');
        }
        $count = 500;
        $faker = Factory::create();
        $password = password_hash('trollB1B1', PASSWORD_DEFAULT);
        for ($i = 0; $i < $count; ++$i) {
            $name = $faker->firstName;
            $gender = Matcha::GENDER[rand(0, 4)];
            $pseudo = $gender . $name;
            $profil = [
                'gender' => $gender,
                'pseudo' => $pseudo,
                'email' => $faker->email(),
                'name' => $name,
                'surname' => $faker->lastName,
                'birthdate' => rand(1970, 2000),
                'sexuality' => Matcha::KIND[rand(0, 2)],
                'biography' => $faker->text(250),
                'password' => $password,
                'activ' => 1,
                'token' => 'a',
                'bot' => 'true',
                'lat' => rand(485500, 490500) / 10000,
                'lng' => rand(21000, 26000) / 10000,
                'popularity' => rand(0, 100),
                'lastlog' => rand(1533224411, time()),
                'publicToken' => $this->form->genPublicToken($pseudo),
                'img' => $this->form->getImg($gender),
            ];
            $_SESSION['pseudo'] = $profil['pseudo'];
            $this->user->setUser($profil);
            $this->user->updateFakeUser($profil);
            $bot = $this->user->getUserByEmail($profil['email']);
            $_SESSION['id'] = $bot['id'];
            for ($j = 0; $j < 5; ++$j) {
                $word = $faker->word();
                $this->tag->setTag($word);
                $tagInfo = $this->tag->getTag($word);
                $this->tag->setUserTag($tagInfo['id']);
            }
            unset($_SESSION['id']);
        }
        session_unset();
        session_destroy();

        return $response->withRedirect('/');
    }
}
