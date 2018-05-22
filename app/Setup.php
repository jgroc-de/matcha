<?php
namespace App;

/**
 * class SetupModel
 * generate fake profil
 */
class Setup extends Constructor
{
    public function init ($request, $response)
    {
        $db = $this->container['settings']['db'];
        $this->debug->ft_print($db);
        $this->dbCreate->exec('DROP DATABASE IF EXISTS ' . $db['dbname']);
        $this->dbCreate->exec('CREATE DATABASE ' . $db['dbname']);
        $this->dbCreate->exec('USE ' . $db['dbname']);
        $file = file_get_contents(__DIR__ . '/../database/matcha.sql');
        $this->db->exec($file);
    } 

    public function fakeFactory ($request, $response)
    {
        $count = 10;
        $profil = array();
        $faker = $this->container->fake;
        //$faker = Faker\Factory::create();
        for ($i = 0; $i < $count; $i++)
        {
            $this->debug->ft_print([$count, $i]);
            $gender = rand(0, 4);
            $orientation = rand(0, 2);
            $forname = $faker->firstName;
            $lastname = $faker->lastName;
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
            $this->container->user->setUser($profil);
            $this->container->user->updateUser($profil);
        }
    }
}
