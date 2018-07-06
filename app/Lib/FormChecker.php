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
    public function check (request $request)
    {
        $post = $_POST;
        if ($this->validator->validate($post, array_keys($post)))
            return $post;
        $this->flash->addMessage('failure', 'burp!');
    }

    /**
     * @param $request requestInterface
     */
    public function checkLogin (request $request)
    {
        if (($post = $this->check($request)))
        {
            if (!empty($account = $this->user->getUser($post['pseudo'])))
            {
                if ($account['activ'] == 0)
                    $this->flash->addMessage('failure', 'account need activation');
                elseif ($this->checkPassword($account['password'], $post['password']))
                {
                    $_SESSION['id'] = $account['id'];
                    $_SESSION['profil'] = $account;
                    $_SESSION['profil']['lattitude'] = floatval($_SESSION['profil']['lattitude']);
                    $_SESSION['profil']['longitude'] = floatval($_SESSION['profil']['longitude']);
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
    public function checkSignup (request $request)
    {
        $user = $this->container->user;
        if (($post = $this->check($request)))
        {
            $post['activ'] = 0;
            $post['token'] = password_hash(random_bytes(6), PASSWORD_DEFAULT);
            if (empty($user->getUser($post['pseudo'])))
            {
                $user->setUser($post);
                $account = $user->getUser($post['pseudo']);
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
    public function checkProfil (request $request)
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
    public function checkPassword ($real, $test)
    {
        return (password_verify($test, $real));
    }
}
