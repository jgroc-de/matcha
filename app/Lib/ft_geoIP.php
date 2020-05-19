<?php

namespace App\Lib;

use App\Model\UserModel;
use GeoIp2\Database\Reader;
use Slim\Container;

class ft_geoIP
{
    /** @var Validator */
    private $validator;
    /** @var Reader */
    private $geoIP;
    /** @var UserModel */
    private $user;

    public function __construct(Validator $validator, Reader $geoIP, UserModel $user)
    {
        $this->validator = $validator;
        $this->geoIP = $geoIP;
        $this->user = $user;
    }

    /**
     * set geolocation by IP
     */
    public function setLatLng(array $post)
    {
        $keys = ['lat', 'lng'];
        if (!$this->validator->validate($post, $keys)) {
            $ip = $this->geoIP->city($_SERVER['REMOTE_ADDR']);
            //$ip = $this->geoIP->city('163.172.250.11');
            //$ip = $this->geoIP->city('82.231.186.199');
            $post['lat'] = $ip->location->latitude;
            $post['lng'] = $ip->location->longitude;
            if (array_key_exists('id', $_SESSION)) {
                $_SESSION['profil']['lattitude'] = floatval($post['lat']);
                $_SESSION['profil']['longitude'] = floatval($post['lng']);
                $this->user->updateGeolocation($post['lat'], $post['lng'], $_SESSION['id']);
            } else {
                $this->user->updateGeolocation($post['lat'], $post['lng'], $post['id']);
            }
        }
    }
}
