<?php
namespace App\Lib;

class ft_geoIP extends \App\Constructor
{
    /**
     * set geolocation by IP
     */
    public function setLatLng($post)
    {
        $keys = ['lat', 'lng'];
        if (!$this->validator->validate($post, $keys))
        {
            //$ip = $this->geoIP->city($_SERVER['REMOTE_ADDR']);
            //$ip = $this->geoIP->city('163.172.250.11');
            $ip = $this->geoIP->city('82.231.186.199');
            $post['lat'] = $ip->location->latitude;
            $post['lng'] = $ip->location->longitude;
            if (array_key_exists('id', $_SESSION))
            {
                $_SESSION['profil']['lattitude'] = floatval($post['lat']);
                $_SESSION['profil']['longitude'] = floatval($post['lng']);
                $this->user->updateGeolocation($post['lat'], $post['lng'], $_SESSION['id']);
            }
            else
                $this->user->updateGeolocation($post['lat'], $post['lng'], $post['id']);
        }
    }
}
