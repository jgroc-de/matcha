<?php

namespace App\Controllers;

use App\Model\UserModel;
use Cloudinary\Uploader;
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
        $imgNbX = intval($args['id']);
        $type = ['image/png', 'image/jpeg', 'image/gif'];
        if (!empty($data['file'])
            && $data['file']->getError() === UPLOAD_ERR_OK
            && $data['file']->getSize() < 4000000
            && in_array($data['file']->getClientMediaType(), $type)
            && $imgNbX >= 1 && $imgNbX <= 5
        ) {
            $imgData = $this->moveUploadedFile('user_img', $data['file']);
            if ($this->userModel->addPicture($imgNbX, $imgData)) {
                $_SESSION['profil']['img' . $imgNbX] = $imgData['secure_url'];

                return $response;
            }
        }

        return $response->withStatus(404);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $nb = intval($args['id']);
        $imgNbX = 'img' . $nb;
        if ($this->userModel->delPicture($nb)) {
            if (strncmp('/user_img/', $_SESSION['profil'][$imgNbX], 5) === 0) {
                unlink(ltrim($_SESSION['profil'][$imgNbX], '/'));
            }
            $_SESSION['profil'][$imgNbX] = '';
            if ($_SESSION['profil']['cloud_id' . $nb]) {
                Uploader::destroy($_SESSION['profil']['cloud_id' . $nb]);
                $_SESSION['profil']['cloud_id' . $nb] = null;
            }
        }

        return $response;
    }

    private function moveUploadedFile(string $directory, UploadedFile $uploadedFile): array
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
        $filename = sprintf('%s.%0.8s', $basename, $extension);
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
        if ($_ENV['CLOUDINARY_URL']) {
            $response = Uploader::upload(__DIR__ . '/../../public/user_img/' . $filename, ['format' => 'webp']);
            unlink(__DIR__ . '/../../public/user_img/' . $filename);
            return $response;
        }

        return ['secure_url' => '/user_img/' . $filename, 'public_id' => False];
    }
}
