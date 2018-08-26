<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Search extends Route
{
    private $kind = ['Rick', 'Morty', 'Beth', 'Summer', 'Jerry'];

    private function searchResponse($age_min, $age_max, $list, $target, $tags, $response, $dist = '10')
    {
        return $this->view->render(
            $response,
            'templates/home/search.html.twig',
            [
                'me' => $_SESSION['profil'],
                'users' => $list,
                'target' => $target,
                'tags' => $tags,
                'age' => ['min' => ((2018 - $age_min) >= 18? 2018 - $age_min:18), 'max' => 2018 - $age_max],
                'distance' => ['1', '5', '10', '50', '100'],
                'notification' => $this->notif->getNotification(),
                'distSel' => $dist
            ]
        );
    }

    public function criteria(Request $request, Response $response, array $args)
    {
        $post = $request->getParsedBody();
        $age_min = 2018 - $post['min'];
        $age_max = 2018 - $post['max'];
        $dist = $post['distance'];
        $target = array_intersect([$post['gender']], $this->kind);
        if (!$target)
        {
            $target = $this->target();
        }
        $list = $this->listByCriteria($age_min, $age_max, $target, $dist);
        if ($_POST['tags'])
        {
            $tags = $_POST['tags'];
        }
        else
        {
            $tags = $this->getTags($list);
        }
        return $this->searchResponse($age_min, $age_max, $list, $target, $tags, $response, $dist);
    }

    public function name(Request $request, Response $response, array $args)
    {
        if (!$this->validator->validate($_POST, ['pseudo']))
        {
            return $response->withRedirect('/search');
        }
        else
        {
            $age_min = 2000;
            $age_max = 1900;
            $list = $this->listByName($_POST['pseudo']);    
            return $this->searchResponse($age_min, $age_max, $list, $this->target(), $this->getTags($list), $response);
        }
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
        //$_SESSION['profil']['sexuality'] = 'hetero';
        $age_min = $_SESSION['profil']['birthdate'] + 5;
        $age_max = $_SESSION['profil']['birthdate'] - 5;
        $list = $this->listDefault($age_min, $age_max);
        return $this->searchResponse($age_min, $age_max, $list, $this->target(), $this->getTags($list), $response);
    }

    private function target()
    {
        switch ($_SESSION['profil']['sexuality'])
        {
            case 'hetero':
                $target = array_diff($this->kind, [$_SESSION['profil']['gender']]);
                break;
            case 'homo':
                $target = [$_SESSION['profil']['gender']];
                break;
            default ;
                $target = $this->kind;
        }
        return $target;
    }

    private function listByCriteria($min, $max, $target, $dist)
    {
        return $this->user->getUserByCriteria($min, $max, $target, $dist);
    }

    private function listByName($name)
    {
        return $this->user->getUserByPseudo($name);
    }

    private function listDefault($age_min, $age_max)
    {
        switch ($_SESSION['profil']['sexuality'])
        {
            case 'hetero':
                $list = $this->user->getUsersHetero($age_min, $age_max, $_SESSION['profil']['gender']);
                break;
            case 'homo':
                $list = $this->user->getUsersHomo($age_min, $age_max, $_SESSION['profil']['gender']);
                break;
            default:
                $list = $this->user->getUsersBi($age_min, $age_max);
        }
        return $list;
    }

    private function getTags($list)
    {
        $tag = array();
        foreach ($list as $value)
        {
            $tmp = $this->tag->getUserTags($value['id']);
            foreach ($tmp as $value2)
            {
                $tag[] = $value2['tag'];
            }
        }
        sort($tag);
        return array_unique($tag);
    }
}
