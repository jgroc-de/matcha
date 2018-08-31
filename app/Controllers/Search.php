<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Search extends Route
{
    private $kind = ['Rick', 'Morty', 'Beth', 'Summer', 'Jerry'];
    private $list = array();
    private $age = array('min' => 2000, 'max' => 0);
    private $targets = ['Rick', 'Morty', 'Beth', 'Summer', 'Jerry'];
    private $tags = array();
    private $popularity = array('min' => 0, 'max' => 100);
    private $dist = 30;
    private $date = 0;
    private $criteria = false;

    public function __invoke(Request $request, Response $response, array $args)
    {
        if (!$_SESSION['profil']['biography'])
        {
            return $response->withRedirect('/editProfil2', 302);
        }
        $this->date = date('Y');
        $this->listDefault();
        return $this->searchResponse($response);
    }

    public function criteria(Request $request, Response $response, array $args)
    {
        $this->criteria = true;
        $this->date = date('Y');
        $post = $request->getParsedBody();
        $this->age['min'] = $this->date - $post['min'];
        $this->age['max'] = $this->date - $post['max'];
        $this->popularity['min'] = $post['Pmin'];
        $this->popularity['max'] = $post['Pmax'];
        $this->dist = $post['distance'];
        $this->getTarget($post);
        $this->listByCriteria();
        foreach ($post as $key => $value)
        {
            if ($key === $value)
            {
                $this->tags[] = $key;
            }
        }
        $this->getTags($post);
        return $this->searchResponse($response);
    }

    public function name(Request $request, Response $response, array $args)
    {
        $date = date('Y');
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

    private function searchResponse($response)
    {
        foreach ($this->list as $key => $user)
        {
            $this->list[$key]['distance'] = $this->angle2distance($user);
            $this->list[$key]['score'] = $this->score($this->list[$key]);
        }
        usort($this->list, array($this, 'sortList'));
        return $this->view->render(
            $response,
            'templates/home/search.html.twig',
            [
                'me' => $_SESSION['profil'],
                'gender' => $this->kind,
                'target' => $this->targets,
                'users' => $this->list,
                'tags' => $this->tags,
                'age' => ['min' => (($this->date - $this->age['min']) >= 18? $this->date - $this->age['min']:18), 'max' => $this->date - $this->age['max']],
                'distSelect' => $this->dist,
                'distance' => ['1', '5', '10', '20', '30', '50', '100', '500', '1000', '10000'],
                'popularity' => $this->popularity,
                'sort' => ['score', 'popularity', 'age desc.', 'age asc.', 'distance', 'tag'],
                'notification' => $this->notif->getNotification(),
                'criteria' => $this->criteria
            ]
        );
    }

    private function listByCriteria()
    {
        $this->list = $this->user->getUserByCriteria($this->age, $this->targets, $this->popularity, $this->distance2angle());
    }

    private function listByName($name)
    {
        $this->list = $this->user->getUserByPseudo($name);
    }

    private function listDefault()
    {
        $this->age['min'] = $_SESSION['profil']['birthdate'] + 10;
        $this->age['max'] = $_SESSION['profil']['birthdate'] - 10;
        $this->list = $this->user->getUsersBySexuality($this->age, $this->distance2angle());
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

    private function getTarget($post = array())
    {
        if (empty($post))
        {
            switch ($_SESSION['profil']['sexuality'])
            {
            case 'hetero':
                $this->targets = array_diff($this->kind, [$_SESSION['profil']['gender']]);
                break;
            case 'homo':
                $this->targets = [$_SESSION['profil']['gender']];
                break;
            }
        }
        else
        {
            $targets = array();
            foreach($this->kind as $key)
            {
                if (array_key_exists($key, $post))
                    $targets[] = $key;
            }
            if (empty($targets))
                $this->getTarget();
            else
                $this->targets = $targets;
        }
    }

    private function getTags($post = array())
    {
        $listTag = $this->tags;
        $myTag = $this->tag->getUserTags($_SESSION['id']);
        foreach ($this->list as $key => $value)
        {
            $list = $this->tag->getUserTags($value['id']);
            $this->list[$key]['tag'] = count($result = $this->intersect($list, $myTag));
            if (!empty($post))
            {
                $void = true;
                foreach ($list as $tag)
                {
                    if (!empty($tag = array_intersect($tag, $post)))
                    {
                        $listTag[] = $tag['tag'];
                        $void = false;
                        break;
                    }
                }
                if ($void)
                    unset($this->list[$key]);
            }
            else
                foreach ($list as $tag)
                    $listTag[] = $tag['tag'];
        }
        sort($listTag);
        $this->tags = array_unique($listTag);
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
    
    private function distance2angle()
    {
        $rayon = 6400;
        $angle = array();

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

    private function score($user)
    {
        return $user['popularity'] / 5 + 5 * pow($user['tag'], $user['tag']) - floor($user['distance'] * 2)  - abs($_SESSION['profil']['birthdate'] - $user['birthdate']);
    }

    public function sortList($a, $b)
    {
        return $b['score'] - $a['score'];
    }

}
