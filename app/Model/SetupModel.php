<?php
namespace App\Model;

/**
 * class SetupModel
 * generate fake profil
 */
class SetupModel extends \App\Constructor
{
    public function init ()
    {
        $db = $this->container['settings']['db'];
        $this->debug->ft_print($db);
        $this->dbCreate->exec('DROP DATABASE IF EXISTS ' . $db['dbname']);
        $this->dbCreate->exec('CREATE DATABASE ' . $db['dbname']);
        $this->dbCreate->exec('USE ' . $db['dbname']);
        $file = file_get_contents(__DIR__ . '/../../database/matcha.sql');
        $this->db->exec($file);
        $this->setup->fakeFactory();
    } 

    public function fakeFactory ($count = 100)
    {
        $profil = array();
        for ($i = 0; $i < $count; $i++)
        {
            $index = rand(0, 4);
            $profil['password'] = 'trollB1';
            $profil['email'] = $this->fake->email();
            $profil['gender'] = $this->characters[$index];
            $profil['pseudo'] = $profil['gender'] . $this->fake->firstName;
            $profil['activ'] = 1;
            $profil['token'] = 'a';
            $this->user->setUser($profil);
        }
    }
}
