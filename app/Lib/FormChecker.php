<?php

namespace App\Lib;

use App\Model\UserModel;

/**
 * check form data
 */
class FormChecker
{
    /** @var Validator */
    private $validator;
    /** @var FlashMessage */
    private $flashMessage;
    /** @var UserModel */
    private $userModel;
    /** @var MailSender */
    private $mail;
    /** @var ft_geoIP */
    private $ft_geoIP;

    public function __construct(
        Validator $validator,
        FlashMessage $flashMessage,
        UserModel $userModel,
        MailSender $mail,
        ft_geoIP $ft_geoIP
    ) {
        $this->validator = $validator;
        $this->flashMessage = $flashMessage;
        $this->userModel = $userModel;
        $this->mail = $mail;
        $this->ft_geoIP = $ft_geoIP;
    }

    public function checkLogin(array $post): bool
    {
        if ($this->validator->validate($post, ['pseudo', 'password']) != 'ok') {
            return false;
        }
        $account = $this->userModel->getUser($post['pseudo']);
        if (empty($account)) {
            $this->flashMessage->addMessage('failure', 'wrong login');

            return false;
        }
        if ($account['activ'] == 0) {
            $this->flashMessage->addMessage('failure', 'account need activation');

            return false;
        }
        if (!$this->testPassword($account['password'], $post['password'])) {
            $this->flashMessage->addMessage('failure', 'wrong password');

            return false;
        }
        $this->setSession($account);

        return true;
    }

    public function setSession(array $user)
    {
        $this->userModel->updateLastlog($user['id']);
        $_SESSION['id'] = $user['id'];
        $_SESSION['profil'] = $user;
        $_SESSION['profil']['lattitude'] = floatval($_SESSION['profil']['lattitude']);
        $_SESSION['profil']['longitude'] = floatval($_SESSION['profil']['longitude']);
        $this->userModel->updatePublicToken();
    }

    public function getImgs(string $gender): string
    {
        $files = scandir('img');
        $imgs = [];
        $gender = strtolower($gender);
        foreach ($files as $file) {
            if (strpos($file, $gender) !== false) {
                $imgs[] = $file;
            }
        }

        return 'img/' . $imgs[rand(0, 4)];
    }

    public function genPublicToken(string $pseudo): string
    {
        return time() . $pseudo . bin2hex(random_bytes(4));
    }

    public function checkResetEmail(array $post)
    {
        if ($this->validator->validate($post, ['email']) == 'ok') {
            $account = $this->userModel->getUserByEmail($post['email']);
            if (!empty($account)) {
                if ($this->mail->sendResetMail($account)) {
                    $this->flashMessage->addMessage('success', 'Check your mail!');
                } else {
                    $this->flashMessage->addMessage('failure', 'Mail not sent');
                }
            } else {
                $this->flashMessage->addMessage('failure', 'unknown mail address…');
            }
        }
    }

    public function checkSignup(array $post): array
    {
        $user = $this->userModel;

        $keys = ['pseudo', 'password', 'email', 'name', 'surname', 'gender'];
        if (($valid = $this->validator->validate($post, $keys)) == 'ok') {
            if ($post['g-recaptcha-response']) {
                $api_url = 'https://www.google.com/recaptcha/api/siteverify?secret='
                . $_ENV['SECRET_CAPTCHA_KEY'] . '&response='
                . $post['g-recaptcha-response']
                . '&remoteip=' . $_SERVER['REMOTE_ADDR'];

                $decode = json_decode(file_get_contents($api_url), true);
            }

            if (empty($decode) || $decode['success'] != true) {
                $this->flashMessage->addMessage('failure', 'you\'re a robot, don\'t lie');
            }
            if ($post['password'] !== $post['password_confirmation']) {
                $this->flashMessage->addMessage('failure', 'Confirm password doesn\'t match');
            } elseif (!empty($user->getUser($post['pseudo']))) {
                $this->flashMessage->addMessage('failure', 'pseudo already taken');
            } elseif (!empty($user->getUserByEmail($post['email']))) {
                $this->flashMessage->addMessage('failure', 'email already taken');
            } else {
                $post['activ'] = 1;
                $post['token'] = password_hash(random_bytes(6), PASSWORD_DEFAULT);
                $post['lat'] = 0;
                $post['lng'] = 0;
                $post['password'] = password_hash($post['password'], PASSWORD_DEFAULT);
                $post['publicToken'] = $this->genPublicToken($post['pseudo']);
                $post['img'] = $this->getImgs($post['gender']);
                $user->setUser($post);
                $post = $user->getUser($post['pseudo']);
                $this->ft_geoIP->setLatLng($post);
                $this->mail->sendValidationMail($post);
                $this->flashMessage->addMessage('success', 'mail sent! Check yourmail box (including trash, spam, whatever…)');
            }
        } else {
            $this->flashMessage->addMessage('failure', $valid . ' is incorrect');
        }

        return $post;
    }

    public function checkContact(array $post)
    {
        if (($valid = $this->validator->validate($post, ['email', 'text'])) == 'ok') {
            if ($post['g-recaptcha-response']) {
                $api_url = 'https://www.google.com/recaptcha/api/siteverify?secret='
                . $_ENV['SECRET_CAPTCHA_KEY'] . '&response='
                . $post['g-recaptcha-response']
                . '&remoteip=' . $_SERVER['REMOTE_ADDR'];

                $decode = json_decode(file_get_contents($api_url), true);
            }

            if (empty($decode) || $decode['success'] != true) {
                $this->flashMessage->addMessage('failure', 'you\'re a robot, don\'t lie');
            } else {
                $this->mail->contactMe($post['text'], $post['email']);
                $this->flashMessage->addMessage('success', 'Thank you!');
            }
        } else {
            $this->flashMessage->addMessage('failure', $valid . ' is incorrect');
        }
    }

    public function checkPwd(array $post)
    {
        if ($this->validator->validate($post, ['password', 'password1']) == 'ok') {
            if ($post['password'] === $post['password1']) {
                $this->userModel->updatePassUser(password_hash($post['password'], PASSWORD_DEFAULT));
                $this->flashMessage->addMessage('success', 'password updated!');
            } else {
                $this->flashMessage->addMessage('fail', 'passwords doesnt match');
            }
        }
    }

    public function checkProfil(array $post): bool
    {
        $keys = ['pseudo', 'email', 'name', 'surname', 'birthdate', 'gender', 'biography', 'sexuality'];
        if ($this->validator->validate($post, $keys) != 'ok') {
            return false;
        }
        if (!empty($this->userModel->getUser($post['pseudo'])) && $post['pseudo'] !== $_SESSION['profil']['pseudo']) {
            $this->flashMessage->addMessage('failure', 'pseudo already taken');

            return false;
        }
        if (!empty($this->userModel->getUserByEmail($post['email'])) && $post['email'] !== $_SESSION['profil']['email']) {
            $this->flashMessage->addMessage('failure', 'email already taken');

            return false;
        }

        return true;
    }

    private function testPassword(string $real, string $test): bool
    {
        return password_verify($test, $real);
    }
}
