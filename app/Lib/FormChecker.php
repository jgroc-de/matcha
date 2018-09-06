<?php

namespace App\Lib;
use \Psr\Http\Message\ServerRequestInterface as Request;

/**
 * check form data
 */
class FormChecker extends \App\Constructor
{
    /**
     * @param $post array
     *
     * @return bool
     */
    public function checkLogin($post)
    {
        if ($this->validator->validate($post, ['pseudo', 'password']))
        {
            if (!empty($account = $this->user->getUser($post['pseudo'])))
            {
                if ($account['activ'] == 0)
                    $this->flash->addMessage('failure', 'account need activation');
                elseif ($this->testPassword($account['password'], $post['password']))
                {
                    $this->user->updateLastlog($account['id']);
                    $_SESSION['id'] = $account['id'];
                    $_SESSION['profil'] = $account;
                    $_SESSION['profil']['lattitude'] = floatval($_SESSION['profil']['lattitude']);
                    $_SESSION['profil']['longitude'] = floatval($_SESSION['profil']['longitude']);
                    $this->user->updatePublicToken();
                    return true;
                }
                else
                    $this->flash->addMessage('failure', 'wrong password');
            }
            else
                $this->flash->addMessage('failure', 'wrong login');
        }
        return false;
    }

    public function checkResetEmail($post)
    {
        if ($this->validator->validate($post, ['email']))
        {
            $account = $this->user->getUserByEmail($post['email']);
            if (!empty($account))
            {
                if($this->mail->sendResetMail($account))
                    $this->flash->addMessage('success', 'Check your mail!');
                else
                    $this->flash->addMessage('failure', 'Mail not sent');
            }
            else
                $this->flash->addMessage('failure', 'unknown mail address…');
        }
    }

    public function checkSignup($post)
    {
        $user = $this->container->user;
        $keys = ['pseudo', 'password', 'email', 'name', 'surname', 'gender'];
        if ($this->validator->validate($post, $keys))
        {
            if (!empty($user->getUser($post['pseudo'])))
                $this->flash->addMessage('failure', 'pseudo already taken');
            else if (!empty($user->getUserByEmail($post['email'])))
                $this->flash->addMessage('failure', 'email already taken');
            else
            {
                $post['activ'] = 0;
                $post['token'] = password_hash(random_bytes(6), PASSWORD_DEFAULT);
                $post['lat'] = 0;
                $post['lng'] = 0;
                $post['password'] = password_hash($post['password'], PASSWORD_DEFAULT);
                $user->setUser($post);
                $post = $user->getUser($post['pseudo']);
                $this->ft_geoIP->setLatLng($post);
                $this->mail->sendValidationMail($post);
                $this->flash->addMessage('success', 'mail sent! Check yourmail box (including trash, spam, whatever…)');
            }
        }
        return ($post);
    }

    public function checkContact($post)
    {
        if ($this->validator->validate($post, ['email', 'text']))
        {
            $this->mail->contactMe($post['text'], $post['email']);
            $this->flash->addMessage('success', 'Thank you!');
        }
    }

    public function checkPwd($post)
    {
        if ($this->validator->validate($post, ['password', 'password1']))
            if ($post['password'] === $post['password1'])
            {
                $this->user->updatePassUser(password_hash($post['password'], PASSWORD_DEFAULT));
                $this->flash->addMessage('success', 'password updated!');
            }
            else
                $this->flash->addMessage('fail', 'passwords doesnt match');
    }

    public function checkProfil($post)
    {
        $keys = array('pseudo', 'email', 'name', 'surname', 'birthdate', 'gender', 'biography', 'sexuality');
        if ($this->validator->validate($post, $keys))
        {
            if (!empty($this->user->getUser($post['pseudo'])) && $post['pseudo'] !== $_SESSION['profil']['pseudo'])
                $this->flash->addMessage('failure', 'pseudo already taken');
            else if (!empty($this->user->getUserByEmail($post['email'])) && $post['email'] !== $_SESSION['profil']['email'])
                $this->flash->addMessage('failure', 'email already taken');
            else
                return true;
        }
        return false;
    }

    /**
     * @param $real string
     * @param $test string
     *
     * @return bool
     */
    private function testPassword($real, $test)
    {
        return (password_verify($test, $real));
    }
}
