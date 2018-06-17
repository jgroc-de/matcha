<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class FakeFactory extends \App\Constructor
{
    public function route(Request $request, Response $response, array $args)
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
            $profil['lat'] = rand(488000, 489100) / 10000;
            $profil['lng'] = rand(22200, 24200) / 10000;
            $profil['popularity'] = rand(0, 100);
            $user->setUser($profil);
            $user->updateUser($profil);
            $user->updatePopularity($profil);
            $bot = $user->getUserByEmail($profil['email']);
            $_SESSION['id'] = $bot['id'];
            for ($j = 0; $j < $i; $j++)
            {
                $this->tag->setUserTag($faker->word());
            }
        }
    }
}
