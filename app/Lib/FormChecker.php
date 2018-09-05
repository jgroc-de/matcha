<?php

namespace App\Lib;
use \Psr\Http\Message\ServerRequestInterface as Request;

/**
 * check form data
 */
class FormChecker extends \App\Constructor
{
    /**
     * à transformer en middleware
     *
     * @param $request requestInterface
     */
    public function check(request $request)
    {
        if ($this->validator->validate($_POST, array_keys($_POST)))
            return $_POST;
        $this->flash->addMessage('failure', 'burp!');
    }

    /**
     * @param $request requestInterface
     */
    public function checkLogin(request $request)
    {
        if (($post = $this->check($request)))
        {
            if (!empty($account = $this->user->getUser($post['pseudo'])))
            {
                if ($account['activ'] == 0)
                    $this->flash->addMessage('failure', 'account need activation');
                elseif ($this->checkPassword($account['password'], $post['password']))
                {
                    $this->user->updateLastlog($account['id']);
                    $_SESSION['id'] = $account['id'];
                    $_SESSION['profil'] = $account;
                    $_SESSION['profil']['lattitude'] = floatval($_SESSION['profil']['lattitude']);
                    $_SESSION['profil']['longitude'] = floatval($_SESSION['profil']['longitude']);
                    //$_SESSION['notification'] = $_SESSION['id'] . time() . random_bytes(4);
                }
                else
                    $this->flash->addMessage('failure', 'wrong password');
            }
            else
                $this->flash->addMessage('failure', 'wrong login');
        }
    }

    /**
     * @param $request requestInterface
     *
     * @return string error if any
     */
    public function checkSignup(request $request)
    {
        $user = $this->container->user;
        if (($post = $this->check($request)))
        {
            $post['activ'] = 0;
            $post['token'] = password_hash(random_bytes(6), PASSWORD_DEFAULT);
            $post['lat'] = 0;
            $post['lng'] = 0;
            if (empty($user->getUser($post['pseudo'])))
            {
                $post['password'] = password_hash($post['password'], PASSWORD_DEFAULT);
                $user->setUser($post);
                $account = $user->getUser($post['pseudo']);
                $_POST['id'] = $account['id'];
                $this->ft_geoIP->setLatLng();
                $this->mail->sendValidationMail($account['pseudo'], $account['email'], $account['token']);
                $this->flash->addMessage('success', 'mail sent! Check yourmail box (including trash, spam, whatever…)');
            }
            else
                $this->flash->addMessage('failure', 'pseudo already taken');
        }
    }

    /**
     * @param $request requestInterface
     *
     * @return string error if any
     */
    public function checkProfil(request $request)
    {
        $user = $this->container->user;
        if (($post = $this->check($request)))
        {
            if (empty($user->getUser($post['pseudo'])) || $post['pseudo'] === $_SESSION['profil']['pseudo'])
            {
                return $post;
            }
            else
                $this->flash->addMessage('failure', 'pseudo already taken');
        }
    }

    /**
     * @param $real string
     * @param $test string
     *
     * @return bool
     */
    public function checkPassword($real, $test)
    {
        return (password_verify($test, $real));
    }
}
