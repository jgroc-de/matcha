<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * class SetupModel
 * generate fake profil
 */
class SetupController extends \App\Constructor
{
    public function init (request $request, response $response)
    {
        $db = $this->container['settings']['db'];
        $this->debug->ft_print($db);
        $pdo = new \PDO('mysql:host=' . $db['host'], $db['user'], $db['pass']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        $pdo->exec('DROP DATABASE IF EXISTS ' . $db['dbname']);
        $pdo->exec('CREATE DATABASE ' . $db['dbname']);
        $pdo->exec('USE ' . $db['dbname']);
        $file = file_get_contents(__DIR__ . '/../../database/matcha.sql');
        $this->db->exec($file);
    } 

    public function fakeFactory (request $request, response $response)
    {
        $count = 10;
        $profil = array();
        $faker = \Faker\Factory::create();
        $user = $this->container->user;
        $debug = $this->container->debug;
        for ($i = 0; $i < $count; $i++)
        {
            $debug->ft_print([$count, $i]);
            $gender = rand(0, 4);
            $orientation = rand(0, 2);
            $forname = $faker->firstName;
            $profil['gender'] = $this->characters[$gender];
            $profil['pseudo'] = $profil['gender'] . $forname;
            $_SESSION['pseudo'] = $profil['gender'] . $forname;
            $profil['email'] = $faker->email();
            $profil['forname'] = $forname;
            $profil['name'] = $faker->lastName;
            $profil['birthdate'] = rand(1970, 2000);
            $profil['sexuality'] = $this->sexualPattern[$orientation];
            $profil['biography'] = $faker->text(250);
            $profil['password'] = 'trollB1';
            $profil['activ'] = 1;
            $profil['token'] = 'a';
            $profil['lattitude'] = rand(488000, 489100) / 10000;
            $profil['longitude'] = rand(22200, 24200) / 10000;
            $user->setUser($profil);
            $user->updateUser($profil);
        }
    }
}
