<?php

namespace App\Controllers;

use App\Lib\FlashMessage;
use App\Lib\FormChecker;
use App\Matcha;
use App\Model\UserModel;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class Authentication
{
    const template = 'templates/logForm/login.html.twig';

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
        FlashMessage $flashMessage,
        FormChecker $formChecker,
        Client $curl,
        UserModel $userModel,
        Twig $view
    ) {
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
        $code = $request->getQueryParam('code');
        if (!empty($code)) {
            try {
                $curlResponse = $this->curl->post(
                    'https://api.intra.42.fr/oauth/token', [
                    'form_params' => [
                            'grant_type' => 'authorization_code',
                            'client_id' => $_ENV['PUB_42_KEY'],
                            'client_secret' => $_ENV['SECRET_42_KEY'],
                            'code' => $code,
                            'redirect_uri' => 'http://localhost:8080/apiLogin',
                        ],
                    ]
                );
                $json = json_decode($curlResponse->getBody());
                var_dump($json);
                $curlRequest = new \GuzzleHttp\Psr7\Request('GET', 'https://api.intra.42.fr/v2/me', [
                    "Authorization: Bearer " . $json->access_token,
                ]);
                $curlResponse = $this->curl->send($curlRequest);
                $json = json_decode($curlResponse->getBody());
                var_dump($json);
                exit();
            } catch (ClientException $error) {
                print($error->getMessage());
            }
            exit();
            return $response->withRedirect('/');
        }

        return $this->login($request, $response, $code);
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
            'PUB_CAPTCHA_KEY' => $_ENV['PUB_CAPTCHA_KEY']
        ], $viewOption);

        return $this->view->render(
            $response,
            self::template,
            $data
        );
    }
}
