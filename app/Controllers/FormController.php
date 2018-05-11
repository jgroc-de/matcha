<?php

namespace App\Controllers;

/**
 * class FormController
 * manage forms data
 */
class FormController extends ContainerClass
{
    /**
     * @param $request requestInterface
     * @param $response responseInterface
     * @return string error if any
     */
    public function checkLogin ($request, $response)
    {
        $post = $request->getParams();
        if ($this->validator->validate($post, 'pseudo', 'password'))
        {
            if (!empty($account = $this->checkPseudo($post['pseudo'])))
            {
                if ($account['activ'] == false)
                    return "coumpte inactif";
                elseif ($this->checkpassword($account['password'], $post['password']))
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
        else
            return "burp!";
    }

    /**
     * @param $request requestInterface
     * @param $response responseInterface
     * @return string error if any
     */
    public function checkSignup ($request, $response)
    {
        $post = $request->getParams();
        if ($this->validator->validate($post, 'pseudo', 'password', 'email', 'gender'))
        {
            if (empty($this->checkPseudo($post['pseudo'])))
            {
                $this->user->setUser($post);
                var_dump('done');
                //$account = $this->getUser($post['pseudo']);
                //$this->sendMail($account);
            }
            else
                var_dump("pseudo deja pris");
        }
        else
            var_dump("burp");
    }

    /**
     * @param $pseudo string;
     * @return array
     */
    public function checkPseudo ($pseudo)
    {
        return $this->user->getUser($pseudo);
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
