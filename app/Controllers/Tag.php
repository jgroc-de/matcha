<?php

namespace App\Controllers;

use App\Lib\Validator;
use App\Model\TagModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Tag
{
    /** @var TagModel */
    private $tag;
    /** @var Validator */
    private $validator;

    public function __construct(TagModel $tagModel, Validator $validator)
    {
        $this->tag = $tagModel;
        $this->validator = $validator;
    }

    public function add(Request $request, Response $response, array $args): Response
    {
        $post = $request->getParsedBody();
        $tag = $this->tag;
        if ($this->validator->validate($post, ['tag'])) {
            $tag->setTag($post['tag']);
            $tagInfo = $tag->getTag($post['tag']);
            if ($tag->setUserTag($tagInfo['id'])) {
                $post = $tag->getUserTagByName($tagInfo['tag'], $_SESSION['id']);
                $response->write($post['id']);

                return $response;
            }
        }

        return $response->withStatus(404);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        if ($this->tag->delUserTag($args['id'], $_SESSION['id'])) {
            return $response;
        }

        return $response->withStatus(404);
    }
}
