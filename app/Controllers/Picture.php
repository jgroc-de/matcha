<?php

namespace App\Controllers;

use App\Model\UserModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\UploadedFile;

class Picture
{
    /** @var UserModel */
    private $userModel;

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    public function add(Request $request, Response $response, array $args): Response
    {
        $data = $request->getUploadedFiles();
        $nb = intval($args['id']);
        $type = ['image/png', 'image/jpeg', 'image/gif'];
        if ($data['file']->getError() === UPLOAD_ERR_OK
            && $data['file']->getSize() < 4000000
            && in_array($data['file']->getClientMediaType(), $type)
            && $nb >= 1 && $nb <= 5) {
            $nb = 'img' . $nb;
            $path = '/user_img/' . $this->moveUploadedFile('user_img', $data['file']);
            if ($this->userModel->addPicture($nb, $path)) {
                $_SESSION['profil'][$nb] = $path;

                return $response;
            }
        }

        return $response->withStatus(404);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        if ($this->userModel->delPicture($nb = 'img' . $args['id'])) {
            if (!strncmp('/user_img/', $_SESSION['profil'][$nb], 5)) {
                unlink(ltrim($_SESSION['profil'][$nb], '/'));
            }
            $_SESSION['profil'][$nb] = '';
        }

        return $response;
    }

    private function moveUploadedFile(string $directory, UploadedFile $uploadedFile): string
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
        $filename = sprintf('%s.%0.8s', $basename, $extension);
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }
}
