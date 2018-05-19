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

    public function fakeFactory ($request, $response, $count = 500)
    {
        $profil = array();
        $faker = $this->fake;
        //$faker = Faker\Factory::create();
        for ($i = 0; $i < $count; $i++)
        {
            $index = rand(0, 4);
            $profil['password'] = 'trollB1';
            $profil['email'] = $faker->email();
            $profil['gender'] = $this->characters[$index];
            $profil['pseudo'] = $profil['gender'] . $faker->firstName;
            $profil['activ'] = 1;
            $profil['token'] = 'a';
            $this->user->setUser($profil);
        }
    }
}
