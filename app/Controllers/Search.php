<?php

namespace App\Controllers;

use App\Lib\FlashMessage;
use App\Lib\Validator;
use App\Model\NotificationModel;
use App\Model\TagModel;
use App\Model\UserModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class Search
{
    private const RADIUS = 6400;
    private const AGE = ['min' => Validator::MIN_AGE, 'max' => Validator::MAX_AGE];
    private const POPULARITY = ['min' => 0, 'max' => 100];
    private const DISTANCE_MIN = 1;
    private const DISTANCE_DEFAULT = 30;

    /** @var FlashMessage */
    private $flash;
    /** @var NotificationModel */
    private $notif;
    /** @var TagModel */
    private $tag;
    /** @var UserModel */
    private $user;
    /** @var Validator */
    private $validator;
    /** @var Twig */
    private $view;

    public function __construct(
        FlashMessage $flashMessage,
        NotificationModel $notificationModel,
        TagModel $tagModel,
        UserModel $userModel,
        Validator $validator,
        Twig $view
    ) {
        $this->flash = $flashMessage;
        $this->notif = $notificationModel;
        $this->tag = $tagModel;
        $this->user = $userModel;
        $this->validator = $validator;
        $this->view = $view;
    }

    public function main(Request $request, Response $response, array $args)
    {
        if (!$_SESSION['profil']['biography']) {
            $this->flash->addMessage(FlashMessage::FAIL, 'Plz complete your profil before searching for targets');

            return $response->withRedirect('/editProfil', 302);
        }

        $list = $this->user->getDefaultUserList($this->distance2angle(self::DISTANCE_DEFAULT));
        if (empty($list)) {
            return $response->withRedirect('/', 302);
        }
        $tags = $this->tag->getUserTags($_SESSION['id']);
        $list = $this->computeMisc($list, $tags);

        return $this->view->render(
            $response,
            'templates/in/search.html.twig',
            [
                'me' => $_SESSION['profil'],
                'target' => $this->getDefaultTarget(),
                'users' => $list,
                'age' => self::AGE,
                'distSelect' => self::DISTANCE_DEFAULT,
                'distance' => ['1', '5', '10', '20', '30', '50', '100', '500', '1000', '10000'],
                'popularity' => self::POPULARITY,
                'sort' => ['score', 'popularity', 'age desc.', 'age asc.', 'distance'],
                'notification' => $this->notif->getNotification(),
                'mapKey' => $_ENV['GMAP_KEY'],
                'tags' => $tags,
            ]
        );
    }

    public function name(Request $request, Response $response, array $args)
    {
        $post = $request->getParsedBody();
        if ($this->validator->validate($post, ['pseudo'])) {
            $list = $this->user->getUserListByPseudo($post['pseudo']);
            if (!empty($list)) {
                $tags = $this->tag->getUserTags($_SESSION['id']);
                $list = $this->computeMisc($list, $tags);

                return $response->withJson($list);
            }
        }

        return $response->withJson([FlashMessage::FAIL => 'nothing Found'], 404);
    }

    public function criteria(Request $request, Response $response, array $args)
    {
        $post = $request->getParsedBody();
        $keys = ['Amin', 'Amax', 'Pmin', 'Pmax', 'distance'];
        if (!$this->validator->validate($post, $keys)) {
            return $response->withJson([FlashMessage::FAIL => 'nothing Found'], 404);
        }
        $date = date('Y');
        $age = [
            'min' => min($date - (int) $post['Amin'], $date - Validator::MIN_AGE),
            'max' => max($date - (int) $post['Amax'], $date - Validator::MAX_AGE),
        ];
        $popularity = [
            'min' => max((int) $post['Pmin'], 0),
            'max' => min((int) $post['Pmax'], 100),
        ];
        $dist = max((int) $post['distance'], self::DISTANCE_MIN);
        $targets = $this->getTarget($post);
        if (empty($targets)) {
            return $response->withJson([FlashMessage::FAIL => 'nothing Found'], 404);
        }
        if ($post['tags']) {
            $userTags = $this->getTags($post);
            if (empty($userTags)) {
                return $response->withJson([FlashMessage::FAIL => 'nothing Found'], 404);
            }
        } else {
            $userTags = [];
        }
        $list = $this->user->getUserListByCriteria($age, $targets, $popularity, $this->distance2angle($dist), $userTags);
        if (empty($list)) {
            return $response->withJson([FlashMessage::FAIL => 'nothing Found'], 404);
        }
        $tags = $this->tag->getUserTags($_SESSION['id']);
        $list = $this->computeMisc($list, $tags);

        return $response->withJson($list);
    }

    private function computeMisc(array $list, array $tags): array
    {
        if (!empty($tags)) {
            $userTags = $this->tag->getCommonUserIdTags($list, $tags);
            foreach ($list as &$user) {
                $user['tag'] = [];
                foreach ($userTags as $userTag) {
                    if ($userTag['id_user'] === $user['id']) {
                        $user['tag'][] = (int) $userTag['id'];
                    }
                }
            }
        }
        foreach ($list as $key => $user) {
            $tmp = [
                'time' => floor((time() - intval($user['lastlog'])) / 3600),
                'distance' => $this->angle2distance($user),
                'score' => $this->computeScore($user),
            ];
            $user = array_merge($user, $tmp);
            $user['lat'] = (float) $user['lat'];
            $user['lng'] = (float) $user['lng'];
            $user['age'] = date('Y') - $user['birthdate'];
            $user['biography'] = str_replace(['\n', '\r'], [' ', ''], $user['biography']);
            $list[$key] = $user;
        }
        usort($list, [$this, 'sortList']);

        return $list;
    }

    private function getTags($post): array
    {
        $tags = [];
        $tmp = explode(' ', $post['tags']);
        foreach ($tmp as $tag) {
            if (strpos($tag, '#') !== 0) {
                continue;
            }
            $slice = substr($tag, 1);
            if (!$slice) {
                continue;
            }
            $idTag = $this->tag->getTagByName($slice);
            if (empty($idTag)) {
                continue;
            }
            $tags[] = $idTag['id'];
        }

        return array_unique($tags);
    }

    private function getTarget(array $post = []): array
    {
        if (!empty($post)) {
            $targets = [];
            foreach (Validator::GENDER as $key) {
                if (array_key_exists($key, $post)) {
                    $targets[] = $key;
                }
            }
            if (!empty($targets)) {
                return array_unique($targets);
            }
        }

        return [];
    }

    private function getDefaultTarget()
    {
        switch ($_SESSION['profil']['sexuality']) {
            case 'hetero':
                return array_diff(Validator::GENDER, [$_SESSION['profil']['gender']]);
            case 'homo':
                return [$_SESSION['profil']['gender']];
            default:
                return Validator::GENDER;
        }
    }

    private function distance2angle(int $dist): array
    {
        $rayonlng = self::RADIUS * cos(deg2rad($_SESSION['profil']['lattitude']));
        $angle = [
            'lat' => rad2deg($dist / self::RADIUS),
            'lng' => rad2deg($dist / $rayonlng),
        ];

        return $angle;
    }

    private function angle2distance(array $user): int
    {
        $lat = deg2rad($_SESSION['profil']['lattitude']);
        $rayonlng = self::RADIUS * cos($lat);
        $latrad = deg2rad($user['lat'] - $_SESSION['profil']['lattitude']);
        $lngrad = deg2rad($user['lng'] - $_SESSION['profil']['longitude']);
        $a = self::RADIUS * sin($latrad);
        $b = $rayonlng * sin($lngrad);

        return $a * $a + $b * $b;
    }

    private function computeScore(array $user): int
    {
        return 1000
            + floor($user['popularity'] / 5)
            + 5 * pow($user['tag'], $user['tag'])
            - $user['time']
            - floor($user['distance'] * 2)
            - abs($_SESSION['profil']['birthdate'] - $user['birthdate']);
    }

    public function sortList(array $a, array $b): int
    {
        return $b['score'] - $a['score'];
    }
}
