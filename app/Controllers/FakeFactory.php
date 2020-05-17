<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FakeFactory extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $count = 500;
        $profil = [];
        $faker = \Faker\Factory::create();
        $user = $this->container->user;
        $tag = $this->container->tag;
        $password = password_hash('trollB1B1', PASSWORD_DEFAULT);
        for ($i = 0; $i < $count; ++$i) {
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
            for ($j = 0; $j < 5; ++$j) {
                $word = $faker->word();
                if (empty($tag->getTag($word))) {
                    $tag->setTag($word);
                }
                $tagInfo = $tag->getTag($word);
                $this->tag->setUserTag($tagInfo['id']);
            }
            unset($_SESSION['id']);
        }
    }
}
