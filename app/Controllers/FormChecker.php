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
     * @return string error if any
     * à transformer en middleware
     */
    public function check (request $request)
    {
        $post = $request->getParams();
        if ($this->validator->validate($post, array_keys($post)))
            return $post;
        var_dump('burp!');
        return null;
    }

    /**
     * @param $request requestInterface
     * @return string error if any
     */
    public function checkLogin (request $request)
    {
        if (($post = $this->check($request)))
        {
            if (!empty($account = $this->user->getUser($post['pseudo'])))
            {
                if ($account['activ'] === false)
                    return "compte inactif";
                elseif ($this->checkPassword($account['password'], $post['password']))
                {
                    $_SESSION['pseudo'] = $account['pseudo'];
                    $_SESSION['id'] = $account['id'];
                }
                else
                    return "wrong password";
            } 
            else
                return "wrong login";
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
                var_dump('mail sent');
            }
            else
                var_dump("pseudo already taken");
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
                var_dump('done');
                return $post;
            }
            else
                var_dump("pseudo already taken");
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
