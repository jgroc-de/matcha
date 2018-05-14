<?php
namespace App\Model;

/**
 * class FakeModel
 * generate fake profil
 */
class FakerModel extends ContainerClass
{
    public function fakeFactory ($count)
    {
        $profil = array();
        for ($i = 0; $i < $count; $i++)
        {
            $profil['pseudo'] = $this->fake->firstName;
            $profil['password'] = 'trollB1';
            $profil['email'] = $this->fake->email();
            $profil['gender'] = 'Rick';
            $this->user->setUser($profil);
        }
    }
}
