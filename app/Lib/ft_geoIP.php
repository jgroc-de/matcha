<?php
namespace App\Lib;

class ft_geoIP extends \App\Constructor
{
    /**
     * set geolocation by IP
     */
    public function setLatLng()
    {
        $keys = ['lat', 'lng'];
        if (!$this->validator->validate($_POST, $keys))
        {
            //$ip = $this->geoIP->city($_SERVER['REMOTE_ADDR']);
            //$ip = $this->geoIP->city('163.172.250.11');
            $ip = $this->geoIP->city('82.231.186.199');
            $_POST['lat'] = $ip->location->latitude;
            $_POST['lng'] = $ip->location->longitude;
            $_SESSION['profil']['lattitude'] = floatval($_POST['lat']);
            $_SESSION['profil']['longitude'] = floatval($_POST['lng']);
            $this->user->updateGeolocation($_POST['lat'], $_POST['lng']);
        }
    }
}
