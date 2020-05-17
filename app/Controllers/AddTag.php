<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AddTag extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $post = $request->getParsedBody();
        $tag = $this->container->tag;
        if ($this->validator->validate($post, ['tag'])) {
            if (empty($tag->getTag($post['tag']))) {
                $tag->setTag($post['tag']);
            }
            $tagInfo = $tag->getTag($post['tag']);
            if (empty($tag->getUserTag($tagInfo['id'], $_SESSION['id']))) {
                $tag->setUserTag($tagInfo['id']);
                $post = $tag->getUserTagByName($tagInfo['tag'], $_SESSION['id']);

                return $response->write($post['id']);
            }
        }

        return $response->withStatus(400);
    }
}
