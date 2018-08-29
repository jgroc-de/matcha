<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Search extends Route
{
    private $kind = ['Rick', 'Morty', 'Beth', 'Summer', 'Jerry'];
    private $list = array();
    private $age = array('min' => 2000, 'max' => 1850);
    private $target = ['Rick', 'Morty', 'Beth', 'Summer', 'Jerry'];
    private $tags =array();
    private $popularity = array('min' => 0, 'max' => 100);

    public function __invoke(Request $request, Response $response, array $args)
    {
        if (!$_SESSION['profil']['biography'])
        {
            return $response->withRedirect('/editProfil2', 302);
        }
        $this->listDefault();
        return $this->searchResponse($response);
    }

    public function criteria(Request $request, Response $response, array $args)
    {
        $post = $request->getParsedBody();
        $this->age['min'] = 2018 - $post['min'];
        $this->age['max'] = 2018 - $post['max'];
        $this->popularity['min'] = $post['Pmin'];
        $this->popularity['max'] = $post['Pmax'];
        $dist = $post['distance'];
        $target = array();
        foreach($this->kind as $key)
        {
            if (array_key_exists($key, $post))
                $target[] = $key;
        }
        if (!$target)
            $this->target();
        else
            $this->target = $target;
        $this->listByCriteria($dist);
        if (array_key_exists('tags', $_POST))
            $this->tags = $_POST['tags'];
        else
            $this->getTags();
        return $this->searchResponse($response, $dist);
    }

    public function name(Request $request, Response $response, array $args)
    {
        if (!$this->validator->validate($_POST, ['pseudo']))
        {
            return $response->withRedirect('/search');
        }
        else
        {
            $this->listByName($_POST['pseudo']);    
            return $this->searchResponse($response);
        }
    }

    private function searchResponse($response, $dist = '10')
    {
        foreach ($this->list as $key => $user)
        {
            $this->list[$key]['distance'] = $this->distance($user);
            $this->list[$key]['score'] = $this->score($this->list[$key]);
        }
        usort($this->list, array($this, 'sortList'));
        return $this->view->render(
            $response,
            'templates/home/search.html.twig',
            [
                'me' => $_SESSION['profil'],
                'gender' => $this->kind,
                'target' => $this->target,
                'users' => $this->list,
                'tags' => $this->tags,
                'age' => ['min' => ((2018 - $this->age['min']) >= 18? 2018 - $this->age['min']:18), 'max' => 2018 - $this->age['max']],
                'distance' => ['1', '5', '10', '50', '100'],
                'popularity' => $this->popularity,
                'sort' => ['score', 'popularity', 'age desc.', 'age asc.', 'distance', 'tag'],
                'notification' => $this->notif->getNotification(),
                'distSel' => $dist
            ]
        );
    }

    private function listByCriteria($dist)
    {
        $this->list = $this->user->getUserByCriteria($this->age, $this->target, $this->popularity, $dist);
    }

    private function listByName($name)
    {
        $this->list = $this->user->getUserByPseudo($name);
    }

    private function listDefault()
    {
        $this->age['min'] = $_SESSION['profil']['birthdate'] + 10;
        $this->age['max'] = $_SESSION['profil']['birthdate'] - 10;
        $this->list = $this->user->getUsersBySexuality($this->age);
        $this->getTarget();
        $this->getTags();
        if ($_SESSION['profil']['sexuality'] === 'bi')
        {
            foreach ($this->list as $key => $value)
            {
                if ($value['sexuality'] === 'homo' && $value['gender'] != $_SESSION['profil']['gender'])
                    array_splice($this->list, $key, 1);
            }
        }
    }

    private function getTarget()
    {
        switch ($_SESSION['profil']['sexuality'])
        {
            case 'hetero':
                $this->target = array_diff($this->kind, [$_SESSION['profil']['gender']]);
                break;
            case 'homo':
                $this->target = [$_SESSION['profil']['gender']];
                break;
        }
    }

    private function getTags()
    {
        $tag = array();
        $myTag = $this->tag->getUserTags($_SESSION['id']);
        foreach ($this->list as $key => $value)
        {
            $tmp = $this->tag->getUserTags($value['id']);
            $this->list[$key]['tag'] = count($result = $this->intersect($tmp, $myTag));
            foreach ($tmp as $value2)
            {
                $tag[] = $value2['tag'];
            }
        }
        sort($tag);
        $this->tags = array_unique($tag);
    }

    private function intersect($a, $b)
    {
        $result = array();
        while ($tmp = array_shift($a))
        {
            $test = false;
            foreach($b as $value)
            {
                if (!($c = strcmp($tmp['tag'], $value['tag'])))
                {
                    $test = true;
                    break;
                }
            }
            if ($test)
                $result[] = $tmp;
        }
        return $result;
    }
    
    private function distance(array $user)
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

    private function score($user)
    {
        return $user['popularity'] / 5 + 5 * pow($user['tag'], $user['tag']) - floor($user['distance'] * 2)  - abs($_SESSION['profil']['birthdate'] - $user['birthdate']);
    }

    public function sortList($a, $b)
    {
        return $b['score'] - $a['score'];
    }

}
