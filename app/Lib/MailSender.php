<?php

namespace App\Lib;

use App\Lib\Mail\MailInterface;

/**
 * managing mail sending
 */
class MailSender
{
    /** @var string */
    private $siteUrl;
    /** @var MailInterface */
    private $mail;

    public function __construct(FlashMessage $flashMessage, MailInterface $mail, string $siteUrl)
    {
        $this->flash = $flashMessage;
        $this->siteUrl = $siteUrl;
        $this->mail = $mail;
    }

    public function sendValidationMail(array $user): bool
    {
        $this->mail->addTo($user['email'], $user['pseudo']);

        $this->mail->setSubject('Matcha Activation link');
        $this->mail->addContent(MailInterface::TYPE_TEXT, 'Hi ' . $user['pseudo'] . ',

            Welcome on match a Rick&Morty!
            To activate your account, plz click on the link bellow or paste it into your web browser.

' . $this->linkGen($user, 'act') . '

            ---------------
            This is an automatic mail, thx to not reply.');

        return $this->mail->send();
    }

    public function sendResetMail(array $user): bool
    {
        $this->mail->addTo($user['email'], $user['pseudo']);

        $this->mail->setSubject('Matcha Reinitialisation link');
        $this->mail->addContent(MailInterface::TYPE_TEXT, 'Hi ' . $user['pseudo'] . ',

            A password reinitialistion request has been made on our website.
            To proceed, plz click on the link bellow or paste it into your web browser.

' . $this->linkGen($user, 'ini') . '

            ---------------
            This is an automatic mail, thx to not reply.');

        return $this->mail->send();
    }

    public function sendDeleteMail(): bool
    {
        $this->mail->addTo($_SESSION['profil']['email'], $_SESSION['profil']['pseudo']);

        $this->mail->setSubject('Delete Request for your account on ' . $_SERVER['SERVER_NAME']);
        $this->mail->addContent(MailInterface::TYPE_TEXT, 'Hi ' . $_SESSION['profil']['pseudo'] . ",

            It's sad but if you really really want to delete your profil, please, click on this last link:
    
" . $this->linkGen($_SESSION['profil'], 'del') . '

            We hope you had some good time on our website and wish you all the best!

        GG,            

            ---------------
            This is an automatic mail, thx to not reply.');

        return $this->mail->send();
    }

    public function sendDataMail(array $data): bool
    {
        if (!empty($data['img'])) {
            foreach ($data['img'] as $file) {
                $this->mail->addAttachment(__DIR__ . '/../../public' . $file);
            }
        }
        unset($data['img']);
        $this->mail->setSubject('Your Datas on ' . $_SERVER['SERVER_NAME']);
        $this->mail->addTo($_SESSION['profil']['email'], $_SESSION['profil']['pseudo']);
        $str = '';
        foreach ($data as $key => $value) {
            $str = $str . "\n$key :\n";
            foreach ($value as $key2 => $info) {
                $str = $str . "    $key2 : $info\n";
            }
        }
        $this->mail->addContent(MailInterface::TYPE_TEXT, 'Hi ' . $_SESSION['profil']['pseudo'] . ",

            Here is all the datas we have about you. Thank you for your trust!
    
    $str

            ---------------
            This is an automatic mail, thx to not reply.");

        return $this->mail->send();
    }

    public function sendDeleteMail2($pseudo, $mail): bool
    {
        $this->dest = $mail;
        $this->mail->addTo($mail, $_SESSION['profil']['pseudo']);
        $this->mail->setSubject('Account successfully deleted  on ' . $_SERVER['SERVER_NAME']);
        $this->mail->addContent(MailInterface::TYPE_TEXT, "Hi $pseudo,

            Last mail from us. We confirm that your account doesn't exist anymore.

            Have a good day!

        GG, 

            ---------------
            This is an automatic mail, thx to not reply.");

        return $this->mail->send();
    }

    public function reportMail($id): bool
    {
        $this->mail->addTo($_ENV['MAIL_OWNER'], MailInterface::OWNER);
        $this->mail->setSubject('User report on ' . $_SERVER['SERVER_NAME']);
        $this->mail->addContent(MailInterface::TYPE_TEXT, "Bonjour maître des 7 océans numériques,

    L'utilisateur " . $_SESSION['id'] . " a dénoncé l'utlisateur $id comme étant un faux compte. Veuillez mettre en place les actions adéquates.

    Glorieuse journée à vous!

            Votre dévoué, " . $_SERVER['SERVER_NAME']);

        return $this->mail->send($_SESSION['profil']['email']);
    }

    public function contactMe($msg, $mail): bool
    {
        if (!empty($_SESSION['profil']['pseudo'])) {
            $user = $_SESSION['profil']['pseudo'];
        } else {
            $user = 'anonymous';
        }
        $this->mail->addTo($_ENV['MAIL_OWNER'], MailInterface::OWNER);
        $this->mail->setSubject('User contact from ' . $_SERVER['SERVER_NAME']);
        $this->mail->addContent(MailInterface::TYPE_TEXT, "Bonjour maître des 7 océans numériques,

    On vous a laissé ce messsage:

         $msg

    Glorieuse journée à vous!

            Votre dévoué, " . $_SERVER['SERVER_NAME']);

        return $this->mail->send($mail, $user);
    }

    private function linkGen(array $user, string $action): string
    {
        return $this->siteUrl . "/validation?action=$action&token=" . rawurlencode($user['token']) . '&id=' . $user['id'];
    }
}
