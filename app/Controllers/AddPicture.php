<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\Http\UploadedFile;

function moveUploadedFile($directory, UploadedFile $uploadedFile)
{
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
    $filename = sprintf('%s.%0.8s', $basename, $extension);

    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

    return $filename;
}

class AddPicture extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $data = $request->getUploadedFiles();
        //var_dump($_FILES);
        $nb = intval($args['id']);
        if ($data['file']->getError() === UPLOAD_ERR_OK && $nb >= 1 && $nb <= 5)
        {
            $path = moveUploadedFile('user_img', $data['file']);
            if ($this->user->addPicture($nb, $path))
            {
                $_SESSION['profil']['img' . $nb] = '/user_img/' . $path;
                $response->write('uploaded');
            }
            else
                $response->write('fail');
        }
        return $response;
    }
}
