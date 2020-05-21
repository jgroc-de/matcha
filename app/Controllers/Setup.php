<?php

namespace App\Controllers;

use App\Matcha;
use App\Model\TagModel;
use App\Model\UserModel;
use Faker\Factory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Setup
{
    /** @var TagModel */
    private $tag;
    /** @var UserModel */
    private $user;
    /** @var array */
    private $settings;
    /** @var \PDO */
    private $db;

    public function __construct(TagModel $tagModel, UserModel $userModel, \PDO $db, array $dbSettings)
    {
        $this->tag = $tagModel;
        $this->user = $userModel;
        $this->settings = $dbSettings;
        $this->db = $db;
    }

    public function initDB(Request $request, Response $response, array $args)
    {
        $db = $this->settings;
        $pdo = new \PDO('mysql:host=' . $db['host'], $db['user'], $db['pass']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        $pdo->exec('DROP DATABASE IF EXISTS ' . $db['dbname']);
        $pdo->exec('CREATE DATABASE ' . $db['dbname']);
        $pdo->exec('USE ' . $db['dbname']);
        $file = file_get_contents(__DIR__ . '/../../database/matcha.sql');
        $this->db->exec($file);
    }

    public function seed(Request $request, Response $response, array $args)
    {
        $count = 500;
        $faker = Factory::create();
        $password = password_hash('trollB1B1', PASSWORD_DEFAULT);
        for ($i = 0; $i < $count; ++$i) {
            $name = $faker->firstName;
            $gender = Matcha::GENDER[rand(0, 4)];
            $profil = [
                'gender' => $gender,
                'pseudo' => $gender . $name,
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
            ];
            $_SESSION['pseudo'] = $profil['pseudo'];
            $this->user->setUser($profil);
            $this->user->updateFakeUser($profil);
            $bot = $this->user->getUserByEmail($profil['email']);
            $_SESSION['id'] = $bot['id'];
            for ($j = 0; $j < 5; ++$j) {
                $word = $faker->word();
                if (empty($this->tag->getTag($word))) {
                    $this->tag->setTag($word);
                }
                $tagInfo = $this->tag->getTag($word);
                $this->tag->setUserTag($tagInfo['id']);
            }
            unset($_SESSION['id']);
        }
    }
}
