<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;

/**
 * class FormController
 * manage forms data
 */
class FormChecker extends \App\Constructor
{
    /**
     * @param $request requestInterface
     * à transformer en middleware
     */
    public function check (request $request)
    {
        $post = $request->getParams();
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
                if ($account['activ'] === false)
                    $this->flash->addMessage('failure', 'inactiv account');
                elseif ($this->checkPassword($account['password'], $post['password']))
                {
                    $_SESSION['pseudo'] = $account['pseudo'];
                    $_SESSION['id'] = $account['id'];
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
     * @return string error if any
     */
    public function checkProfil (request $request)
    {
        $user = $this->container->user;
        if (($post = $this->check($request)))
        {
            if (empty($user->getUser($post['pseudo'])) || $post['pseudo'] === $_SESSION['pseudo'])
            {
                $user->updateUser($post);
                $_SESSION['pseudo'] = $post['pseudo']; 
                $this->flash->addMessage('success', 'done');
                return $post;
            }
            else
                $this->flash->addMessage('failure', 'pseudo already taken');
        }
    }

    /**
     * @param $real string
     * @param $test string
     * @return bool
     */
    public function checkPassword ($real, $test)
    {
        return ($real === $test);
    }
}
