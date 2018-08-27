<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class UpdateGeolocation extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        if (!$args['auth'])
        {
            $this->ft_geoIP->setLatLng();
        }
        if ($this->user->updateGeolocation($_POST['lat'], $_POST['lng']))
        {
            $_SESSION['profil']['lattitude'] = floatval($_POST['lat']);
            $_SESSION['profil']['longitude'] = floatval($_POST['lng']);
            $response->write(json_encode($_POST, JSON_NUMERIC_CHECK));
            return $response;
        }
        return $response->withStatus(400);
    }
}
