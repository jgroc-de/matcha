<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Search
{
    private const AGE = ['min' => 18, 'max' => 118];
    private const POPULARITY = ['min' => 0, 'max' => 100];
    private const TARGETS = ['Rick', 'Morty', 'Beth', 'Summer', 'Jerry'];
    private const DISTANCE_MIN = 1;
    private const DISTANCE_DEFAULT = 30;
    private const KIND = ['Rick', 'Morty', 'Beth', 'Summer', 'Jerry'];
    private $list = [];
    private $age = ['min' => 2000, 'max' => 1990];
    private $tags = [];
    private $popularity = ['min' => 0, 'max' => 100];
    private $date = 0;
    private $criteria = false;
    private $name = '';

    private $container;

    public function __construct(
        $container
    ) {
        $this->container = $container;
    }

    public function __get($name)
    {
        return $this->container->get($name);
    }

    public function main(Request $request, Response $response, array $args)
    {
        if (!$_SESSION['profil']['biography']) {
            $this->flash->addMessage('failure', 'Plz complete your profil before searching for targets');

            return $response->withRedirect('/editProfil', 302);
        }

        $list = $this->listDefault();

        return $this->view->render(
            $response,
            'templates/in/search.html.twig',
            [
                'me' => $_SESSION['profil'],
                'gender' => self::KIND,
                'target' => $this->getTarget([]),
                'users' => $list,
                'age' => self::AGE,
                'distSelect' => self::DISTANCE_DEFAULT,
                'distance' => ['1', '5', '10', '20', '30', '50', '100', '500', '1000', '10000'],
                'popularity' => self::POPULARITY,
                'sort' => ['score', 'popularity', 'age desc.', 'age asc.', 'distance'],
                'notification' => $this->notif->getNotification(),
                'mapKey' => $_ENV['GMAP_KEY'],
            ]
        );
    }

    public function name(Request $request, Response $response, array $args)
    {
        $getParams = $request->getQueryParams();
        if ($this->validator->validate($getParams, ['pseudo'])) {
            $list = $this->user->getUserByPseudo($getParams['pseudo']);

            return $response->withJson($list);
        }

        return $response->withJson([], 404);
    }

    public function criteria(Request $request, Response $response, array $args)
    {
        $post = $request->getParsedBody();
        $keys = ['Amin', 'Amax', 'Pmin', 'Pmax', 'distance'];
        if ($this->validator->validate($post, $keys)) {
            $date = date('Y');
            $age = [
                'min' => max($date - $post['min'], self::AGE['min']),
                'max' => min($date - $post['max'], self::AGE['max']),
            ];
            $popularity = [
                'min' => max($post['Pmin'], 0),
                'max' => min($post['Pmax'], 100),
            ];
            $dist = max((int) $post['distance'], self::DISTANCE_MIN);
            $targets = $this->getTarget($post);
            $list = $this->user->getUserByCriteria($age, $targets, $popularity, $this->distance2angle($dist));

            return $response->withJson($list);
        }

        return $response->withJson([], 404);
    }

    private function filterList(array $list)
    {
        foreach ($list as $key => $user) {
            $tmp = [
                'time' => floor((time() - intval($user['lastlog'])) / 3600),
                'distance' => $this->angle2distance($user),
                'score' => $this->score($list[$key]),
            ];
            $list[$key] = array_merge($list[$key], $tmp);
        }
    }

    /**
     * search 'listCmp' for used
     *
     * @param $a
     * @param $b
     * @return mixed
     */
    private function listCmp($a, $b)
    {
        return $a['id'] - $b['id'];
    }

    private function listDefault(): array
    {
        $age = [
            'min' => $_SESSION['profil']['birthdate'] + 10,
            'max' => $_SESSION['profil']['birthdate'] - 10,
        ];
        $list = $this->user->getDefaultUserList($age, $this->distance2angle(self::DISTANCE_DEFAULT));
        $this->filterList($list);
        usort($list, [$this, 'sortList']);

        return $list;
    }

    private function getTarget(array $post = [])
    {
        if (!empty($post)) {
            $targets = [];
            foreach (self::KIND as $key) {
                if (array_key_exists($key, $post)) {
                    $targets[] = $key;
                }
            }
            if (!empty($targets)) {
                return array_unique($targets);
            }
        }
        switch ($_SESSION['profil']['sexuality']) {
            case 'hetero':
                return array_diff(self::KIND, [$_SESSION['profil']['gender']]);
            case 'homo':
                return [$_SESSION['profil']['gender']];
            default:
                return self::TARGETS;
        }
    }

    private function distance2angle(int $dist)
    {
        $rayon = 6400;
        $angle = [];

        $rayonlng = $rayon * cos(deg2rad($_SESSION['profil']['lattitude']));
        $angle['lat'] = rad2deg($dist / $rayon);
        $angle['lng'] = rad2deg($dist / $rayonlng);

        return $angle;
    }

    private function angle2distance(array $user)
    {
        $rayon = 6400;

        $lat = deg2rad($_SESSION['profil']['lattitude']);
        $rayonlng = $rayon * cos($lat);
        $latrad = deg2rad($user['lattitude'] - $_SESSION['profil']['lattitude']);
        $lngrad = deg2rad($user['longitude'] - $_SESSION['profil']['longitude']);
        $a = $rayon * sin($latrad);
        $b = $rayonlng * sin($lngrad);

        return sqrt($a * $a + $b * $b);
    }

    private function score($user): int
    {
        return 1000
            + floor($user['popularity'] / 5)
            + 5 * pow($user['tag'], $user['tag'])
            - $user['time']
            - floor($user['distance'] * 2)
            - abs($_SESSION['profil']['birthdate'] - $user['birthdate']);
    }

    public function sortList($a, $b)
    {
        return $b['score'] - $a['score'];
    }
}
