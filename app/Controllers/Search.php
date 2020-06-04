<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Search
{
    private $kind = ['Rick', 'Morty', 'Beth', 'Summer', 'Jerry'];
    private $list = [];
    private $age = ['min' => 2000, 'max' => 1990];
    private $targets = ['Rick', 'Morty', 'Beth', 'Summer', 'Jerry'];
    private $tags = [];
    private $popularity = ['min' => 0, 'max' => 100];
    private $dist = 30;
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
        $this->listDefault();

        return $this->searchResponse($response);
    }

    public function name(Request $request, Response $response, array $args)
    {
        $name = $request->getQueryParams();
        if ($name && $this->validator->validate($name, ['pseudo'])) {
            $this->list = $this->user->getUserByPseudo($name['pseudo']);
            $this->name = $name['pseudo'];
            $this->listDefault();

            return $this->searchResponse($response);
        }

        return ($this->notFoundHandler)($request, $response);
    }

    private function searchResponse(Response $response)
    {
        $this->filterList();
        usort($this->list, [$this, 'sortList']);

        return $this->view->render(
            $response,
            'templates/in/search.html.twig',
            [
                'me' => $_SESSION['profil'],
                'gender' => $this->kind,
                'target' => $this->targets,
                'users' => $this->list,
                'tags' => $this->tags,
                'age' => [
                    'min' => (($this->date - $this->age['min']) >= 18 ? $this->date - $this->age['min'] : 18),
                    'max' => $this->date - $this->age['max']
                ],
                'distSelect' => $this->dist,
                'distance' => ['1', '5', '10', '20', '30', '50', '100', '500', '1000', '10000'],
                'popularity' => $this->popularity,
                'sort' => ['score', 'popularity', 'age desc.', 'age asc.', 'distance', 'tag'],
                'notification' => $this->notif->getNotification(),
                'criteria' => $this->criteria,
                'mapKey' => $_ENV['GMAP_KEY'],
                'name' => $this->name,
            ]
        );
    }

    public function criteria(Request $request, Response $response, array $args)
    {
        $post = $request->getParsedBody();
        $keys = ['min', 'max', 'Pmin', 'Pmax', 'distance'];
        if ($this->validator->validate($post, $keys)) {
            $this->criteria = true;
            $this->date = date('Y');
            $this->age['min'] = $this->date - $post['min'];
            $this->age['max'] = $this->date - $post['max'];
            $this->popularity['min'] = $post['Pmin'];
            $this->popularity['max'] = $post['Pmax'];
            $this->dist = $post['distance'];
            $this->getTarget($post);
            print_r($this);
            $this->listByCriteria();
            foreach ($post as $key => $value) {
                if ($key === $value) {
                    $this->tags[] = $key;
                }
            }
            $this->getTags($post);

            return $this->searchResponse($response);
        }

        return $response->withStatus(404);
    }

    private function filterList()
    {
        $bList = $this->blacklist->getAllBlacklist();
        $blacklist = [];
        foreach ($bList as $id) {
            if ($id['id_user'] === $_SESSION['id']) {
                $blacklist[] = $id['id_user_bl'];
            } else {
                $blacklist[] = $id['id_user'];
            }
        }
        $friends = $this->friends->getfriends($_SESSION['id']);
        $this->list = array_udiff($this->list, $friends, [$this, 'listCmp']);
        $friendsReq = $this->friends->getFriendReqs($_SESSION['id']);
        $this->list = array_udiff($this->list, $friendsReq, [$this, 'listCmp']);
        foreach ($this->list as $key => $user) {
            if (in_array($user['id'], $blacklist)) {
                unset($this->list[$key]);
            } else {
                $this->list[$key]['time'] = floor((time() - intval($user['lastlog'])) / 3600);
                $this->list[$key]['distance'] = $this->angle2distance($user);
                $this->list[$key]['score'] = $this->score($this->list[$key]);
            }
        }
    }

    private function listCmp($a, $b)
    {
        return $a['id'] - $b['id'];
    }

    private function listByCriteria()
    {
        $this->list = $this->user->getUserByCriteria($this->age, $this->targets, $this->popularity, $this->distance2angle());
    }

    private function listDefault()
    {
        $this->date = date('Y');
        $this->age['min'] = $_SESSION['profil']['birthdate'] + 10;
        $this->age['max'] = $_SESSION['profil']['birthdate'] - 10;
        $this->list = $this->user->getUsersBySexuality($this->age, $this->distance2angle());
        $this->getTarget();
        $this->getTags();
        if ($_SESSION['profil']['sexuality'] === 'bi') {
            foreach ($this->list as $key => $value) {
                if ($value['sexuality'] === 'homo' && $value['gender'] != $_SESSION['profil']['gender']) {
                    array_splice($this->list, $key, 1);
                }
            }
        }
    }

    private function getTarget($post = [])
    {
        if (empty($post)) {
            switch ($_SESSION['profil']['sexuality']) {
            case 'hetero':
                $this->targets = array_diff($this->kind, [$_SESSION['profil']['gender']]);
                break;
            case 'homo':
                $this->targets = [$_SESSION['profil']['gender']];
                break;
            }
        } else {
            $targets = [];
            foreach ($this->kind as $key) {
                if (array_key_exists($key, $post)) {
                    $targets[] = $key;
                }
            }
            if (empty($targets)) {
                $this->getTarget();
            } else {
                $this->targets = $targets;
            }
        }
    }

    private function getTags($post = [])
    {
        $listTag = $this->tags;
        $myTag = $this->tag->getUserTags($_SESSION['id']);
        foreach ($this->list as $key => $value) {
            $list = $this->tag->getUserTags($value['id']);
            $this->list[$key]['tag'] = count($result = $this->intersect($list, $myTag));
            if (!empty($post)) {
                $void = true;
                foreach ($list as $tag) {
                    $tag = array_intersect($tag, $post);
                    if (!empty($tag['tag'])) {
                        $listTag[] = $tag['tag'];
                        $void = false;
                        break;
                    }
                }
                if ($void) {
                    unset($this->list[$key]);
                }
            } else {
                foreach ($list as $tag) {
                    $listTag[] = $tag['tag'];
                }
            }
        }
        sort($listTag);
        $this->tags = array_unique($listTag);
    }

    private function intersect($a, $b)
    {
        $result = [];
        while ($tmp = array_shift($a)) {
            $test = false;
            foreach ($b as $value) {
                if (!($c = strcmp($tmp['tag'], $value['tag']))) {
                    $test = true;
                    break;
                }
            }
            if ($test) {
                $result[] = $tmp;
            }
        }

        return $result;
    }

    private function distance2angle()
    {
        $rayon = 6400;
        $angle = [];

        $rayonlng = $rayon * cos(deg2rad($_SESSION['profil']['lattitude']));
        $angle['lat'] = rad2deg($this->dist / $rayon);
        $angle['lng'] = rad2deg($this->dist / $rayonlng);

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
