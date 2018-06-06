<?php
namespace App\Lib;

class ft_geoIP extends \App\Constructor
{
    public function setLatLng()
    {
        $keys = ['lat', 'lng'];
        if (!$this->validator->validate($_POST, $keys))
        {
            //$ip = $this->geoIP->city($_SERVER['REMOTE_ADDR']);
            $ip = $this->geoIP->city('163.172.250.11');
            $_POST['lat'] = $ip->location->latitude;
            $_POST['lng'] = $ip->location->longitude;
        }
    }
}
