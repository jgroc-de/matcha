<?php

namespace App\Controllers;

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
    public function check ($request)
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
    public function checkLogin ($request)
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
                    return "mauvais mot de passe";
            } 
            else
                return "mauvais login";
        }
    }

    /**
     * @param $request requestInterface
     * @return string error if any
     */
    public function checkSignup ($request)
    {
        if (($post = $this->check($request)))
        {
            $post['activ'] = 0;
            $post['token'] = password_hash(random_bytes(6), PASSWORD_DEFAULT);
            if (empty($this->user->getuser($post['pseudo'])))
            {
                $this->user->setUser($post);
                $account = $this->user->getUser($post['pseudo']);
                $this->mail->sendValidationMail($account['pseudo'], $account['email'], $account['token']);
                var_dump('mail sent');
            }
            else
                var_dump("pseudo deja pris");
        }
    }

    /**
     * @param $request requestInterface
     * @return string error if any
     */
    public function checkProfil ($request)
    {
        if (($post = $this->check($request)))
        {
            if (empty($this->user->getUser($post['pseudo'])) || $post['pseudo'] === $_SESSION['pseudo'])
            {
                $this->user->updateUser($post);
                $_SESSION['pseudo'] = $post['pseudo']; 
                var_dump('done');
                return $post;
            }
            else
                var_dump("pseudo deja pris");
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
