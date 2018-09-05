<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class FakeFactory extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $count = 500;
        $profil = array();
        $faker = \Faker\Factory::create();
        $user = $this->container->user;
        $password = password_hash('trollB1B1', PASSWORD_DEFAULT);
        for ($i = 0; $i < $count; $i++)
        {
            $gender = rand(0, 4);
            $orientation = rand(0, 2);
            $name = $faker->firstName;
            $profil['gender'] = $this->characters[$gender];
            $profil['pseudo'] = $profil['gender'] . $name;
            $_SESSION['pseudo'] = $profil['gender'] . $name;
            $profil['email'] = $faker->email();
            $profil['name'] = $name;
            $profil['surname'] = $faker->lastName;
            $profil['birthdate'] = rand(1970, 2000);
            $profil['sexuality'] = $this->sexualPattern[$orientation];
            $profil['biography'] = $faker->text(250);
            $profil['password'] = $password;
            $profil['activ'] = 1;
            $profil['token'] = 'a';
            $profil['bot'] = 'true';
            $profil['lat'] = rand(485500, 490500) / 10000;
            $profil['lng'] = rand(21000, 26000) / 10000;
            $profil['popularity'] = rand(0, 100);
            $profil['lastlog'] = rand(1533224411, time());
            $user->setUser($profil);
            $user->updateFakeUser($profil);
            $bot = $user->getUserByEmail($profil['email']);
            $_SESSION['id'] = $bot['id'];
            for ($j = 0; $j < 5; $j++)
            {
                $this->tag->setUserTag($faker->word());
            }
            unset($_SESSION['id']);
        }
    }
}
