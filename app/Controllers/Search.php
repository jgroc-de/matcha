<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Search extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $target = ['Rick', 'Morty', 'Beth', 'Summer', 'Jerry'];
        //$_SESSION['profil']['sexuality'] = 'hetero';
        $age_min = $_SESSION['profil']['birthdate'] + 5;
        $age_max = $_SESSION['profil']['birthdate'] - 5;
        switch ($_SESSION['profil']['sexuality'])
        {
            case 'hetero':
                $target = array_diff($target, [$_SESSION['profil']['gender']]);
                $list = $this->user->getUsersHetero($age_min, $age_max, $_SESSION['profil']['gender']);
                break;
            case 'homo':
                $target = [$_SESSION['profil']['gender']];
                $list = $this->user->getUsersHomo($age_min, $age_max, $_SESSION['profil']['gender']);
                break;
            default:
                $list = $this->user->getUsersBi($age_min, $age_max);
        }
        return $this->view->render(
            $response,
            'templates/home/search.html.twig',
            [
                'me' => $_SESSION['profil'],
                'users' => $list,
                'target' => $target,
                'tags' => $this->getTags($list),
                'age' => ['min' => ((2018 - $age_min) >= 18? 2018 - $age_min:18), 'max' => 2018 - $age_max],
                'distance' => ['1', '5', '10', '50', '100'],
                'distSel' => '10'
            ]
        );
    }

    private function getTags($list)
    {
        foreach ($list as $value)
        {
            $tmp =$this->tag->getUserTags($value['id']);
            foreach ($tmp as $value2)
            {
                $tag[] = $value2['tag'];
            }
        }
        sort($tag);
        return array_unique($tag);
    }
}
