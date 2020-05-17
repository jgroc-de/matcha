<?php

namespace App\Lib;

use App\Constructor;

/**
 * check form data
 */
class FormChecker extends Constructor
{
    public function checkLogin(array $post): bool
    {
        if (!$this->validator->validate($post, ['pseudo', 'password'])) {
            return false;
        }
        if (empty($account = $this->user->getUser($post['pseudo']))) {
            $this->flash->addMessage('failure', 'wrong login');
            return false;
        }
        if ($account['activ'] == 0) {
            $this->flash->addMessage('failure', 'account need activation');
            return false;
        }
        if (!$this->testPassword($account['password'], $post['password'])) {
            $this->flash->addMessage('failure', 'wrong password');
            return false;
        }
        $this->user->updateLastlog($account['id']);
        $_SESSION['id'] = $account['id'];
        $_SESSION['profil'] = $account;
        $_SESSION['profil']['lattitude'] = floatval($_SESSION['profil']['lattitude']);
        $_SESSION['profil']['longitude'] = floatval($_SESSION['profil']['longitude']);
        $this->user->updatePublicToken();

        return true;
    }

    public function checkResetEmail(array $post)
    {
        if ($this->validator->validate($post, ['email'])) {
            $account = $this->user->getUserByEmail($post['email']);
            if (!empty($account)) {
                if ($this->mail->sendResetMail($account)) {
                    $this->flash->addMessage('success', 'Check your mail!');
                } else {
                    $this->flash->addMessage('failure', 'Mail not sent');
                }
            } else {
                $this->flash->addMessage('failure', 'unknown mail address…');
            }
        }
    }

    public function checkSignup(array $post): array
    {
        $user = $this->user;
        $keys = ['pseudo', 'password', 'email', 'name', 'surname', 'gender'];
        if ($this->validator->validate($post, $keys)) {
            if (!empty($user->getUser($post['pseudo']))) {
                $this->flash->addMessage('failure', 'pseudo already taken');
            } elseif (!empty($user->getUserByEmail($post['email']))) {
                $this->flash->addMessage('failure', 'email already taken');
            } else {
                $post['activ'] = 1;
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

        return $post;
    }

    public function checkContact(array $post)
    {
        if ($this->validator->validate($post, ['email', 'text'])) {
            $this->mail->contactMe($post['text'], $post['email']);
            $this->flash->addMessage('success', 'Thank you!');
        }
    }

    public function checkPwd(array $post)
    {
        if ($this->validator->validate($post, ['password', 'password1'])) {
            if ($post['password'] === $post['password1']) {
                $this->user->updatePassUser(password_hash($post['password'], PASSWORD_DEFAULT));
                $this->flash->addMessage('success', 'password updated!');
            } else {
                $this->flash->addMessage('fail', 'passwords doesnt match');
            }
        }
    }

    public function checkProfil(array $post): bool
    {
        $keys = ['pseudo', 'email', 'name', 'surname', 'birthdate', 'gender', 'biography', 'sexuality'];
        if (!$this->validator->validate($post, $keys)) {
            return false;
        }
        if (!empty($this->user->getUser($post['pseudo'])) && $post['pseudo'] !== $_SESSION['profil']['pseudo']) {
            $this->flash->addMessage('failure', 'pseudo already taken');
            return false;
        }
        if (!empty($this->user->getUserByEmail($post['email'])) && $post['email'] !== $_SESSION['profil']['email']) {
            $this->flash->addMessage('failure', 'email already taken');
            return false;
        }
        return true;
    }

    private function testPassword(string $real, string $test): bool
    {
        return password_verify($test, $real);
    }
}
