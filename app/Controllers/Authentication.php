<?php

namespace App\Controllers;

use App\Lib\_42API;
use App\Lib\FlashMessage;
use App\Lib\FormChecker;
use App\Lib\googleAPI;
use App\Matcha;
use App\Model\UserModel;
use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class Authentication
{
    const template = 'templates/logForm/login.html.twig';

    /** @var ContainerInterface */
    private $container;
    /** @var Client */
    private $curl;
    /** @var FormChecker */
    private $form;
    /** @var FlashMessage */
    private $flashMessage;
    /** @var UserModel */
    private $userModel;
    /** @var Twig */
    private $view;

    public function __construct(
        ContainerInterface $container,
        FlashMessage $flashMessage,
        FormChecker $formChecker,
        Client $curl,
        UserModel $userModel,
        Twig $view
    ) {
        $this->container = $container;
        $this->flashMessage = $flashMessage;
        $this->form = $formChecker;
        $this->curl = $curl;
        $this->userModel = $userModel;
        $this->view = $view;
    }

    public function login(Request $request, Response $response, array $post): Response
    {
        if (!isset($post['gender'])) {
            $post['gender'] = Matcha::GENDER[random_int(0, 4)];
        }

        return $this->renderSettings($response, $post, ['login' => true]);
    }

    public function postLogin(Request $request, Response $response, array $args): Response
    {
        $post = $request->getParsedBody();
        if ($this->form->checkLogin($post)) {
            return $response->withRedirect('/');
        }

        return $this->login($request, $response, $post);
    }

    public function apiLogin(Request $request, Response $response, array $args): Response
    {
        switch ($args['name']) {
            case '42':
                $client = new _42API($this->container);
                $token = $request->getQueryParam('code');
                if (!$token) {
                    return $response->withRedirect('/');
                }
                $url = $client->loginToApi($token);

                return $response->withRedirect($url);
            case 'google':
                $client = new googleAPI($this->container);
                $body = $request->getParsedBody();
                $token = $body['idtoken'];
                if (!$token) {
                    return $response->withStatus(404);
                }
                $url = $client->loginToApi($token);
                $response->write($url);
        }

        return $response;
    }

    public function logout(Request $request, Response $response, array $args): Response
    {
        $this->userModel->updateLastlog($_SESSION['id']);
        session_unset();
        session_destroy();

        return $this->view->render($response, 'templates/logForm/logout.html.twig', ['logout' => true]);
    }

    public function signup(Request $request, Response $response, array $post): Response
    {
        return $this->renderSettings($response, $post, ['signup' => true]);
    }

    public function postSignup(Request $request, Response $response, array $args): Response
    {
        $post = $request->getParsedBody();
        $post = $this->form->checkSignup($post);

        return $this->signup($request, $response, $post);
    }

    public function resetPassword(Request $request, Response $response, array $post): Response
    {
        return $this->renderSettings($response, $post, ['reset' => true]);
    }

    public function postPassword(Request $request, Response $response, array $args): Response
    {
        $post = $request->getParsedBody();
        $this->form->checkResetEmail($post);

        return $this->resetPassword($request, $response, $post);
    }

    private function renderSettings(Response $response, array $post, array $viewOption): Response
    {
        $data = array_merge([
            'flash' => $this->flashMessage->getMessages(),
            'post' => $post,
            'characters' => Matcha::GENDER,
            'PUB_42_KEY' => $_ENV['PUB_42_KEY'],
            'PUB_CAPTCHA_KEY' => $_ENV['PUB_CAPTCHA_KEY'],
        ], $viewOption);

        return $this->view->render(
            $response,
            self::template,
            $data
        );
    }
}
