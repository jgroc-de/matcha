<?php

namespace App\Lib;

/**
 * managing mail sending
 */
class MailSender
{
    const USER = 'webmestre';
    const EXP = 'jgroc2s@free.fr';
    const PASS = '';
    const PORT = ':8080';
    private $dest = 'jgroc2s@free.fr';
    private $subject = '';
    private $message = '';
    private $files = [];

    /**
     * @param string $this->dest email address
     * @param string $this->subject
     * @param string $this->message
     *
     * @return string for success or failure
     */
    public function sendMail(string $replyTo = self::EXP, string $user = self::USER)
    {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        $mail->IsSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = 'smtp.free.fr';
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        $mail->Username = self::EXP;
        $mail->Password = self::PASS;
        $mail->CharSet = 'UTF-8';
        if (!empty($this->files)) {
            foreach ($this->files as $file) {
                $mail->addAttachment(__DIR__ . '/../../public' . $file);
            }    // Optional name
        }
        $mail->addReplyTo($replyTo, 'First Last');
        $mail->setFrom(self::EXP, $user);
        $mail->addAddress($this->dest);
        $mail->Subject = $this->subject;
        $mail->Body = $this->message;

        return $mail->send();
    }

    /**
     * @param string $login name
     * @param string $this->dest email address
     * @param string $token hashed key
     */
    public function sendValidationMail(array $user)
    {
        $this->dest = $user['email'];
        $this->subject = 'Matcha Activation link';
        $this->message = 'Hi ' . $user['pseudo'] . ',

            Welcome on match a Rick&Morty!
            To activate your account, plz click on the link bellow or paste it into your web browser.

' . $this->linkGen($user, 'act') . '

            ---------------
            This is an automatic mail, thx to not reply.';

        return $this->sendMail();
    }

    /**
     * @param string $login name
     * @param string $this->dest email address
     * @param string $token hashed key
     */
    public function sendResetMail(array $user)
    {
        $this->dest = $user['email'];
        $this->subject = 'Matcha Reinitialisation link';
        $this->message = 'Hi ' . $user['pseudo'] . ',

            A password reinitialistion request has been made on our website.
            To proceed, plz click on the link bellow or paste it into your web browser.

' . $this->linkGen($user, 'ini') . '

            ---------------
            This is an automatic mail, thx to not reply.';

        return $this->sendMail();
    }

    public function sendDeleteMail()
    {
        $this->dest = $_SESSION['profil']['email'];
        $this->subject = 'Delete Request for your account on ' . $_SERVER['SERVER_NAME'];
        $this->message = 'Hi ' . $_SESSION['profil']['pseudo'] . ",

            It's sad but if you really really want to delete your profil, please, click on this last link:
    
" . $this->linkGen($_SESSION['profil'], 'del') . '

            We hope you had some good time on our website and wish you all the best!

        GG,            

            ---------------
            This is an automatic mail, thx to not reply.';

        return $this->sendMail();
    }

    public function sendDataMail(array $data)
    {
        $this->files = $data['img'];
        unset($data['img']);
        $this->subject = 'Your Datas on ' . $_SERVER['SERVER_NAME'];
        $this->dest = $_SESSION['profil']['email'];
        $str = '';
        foreach ($data as $key => $value) {
            $str = $str . "\n$key :\n";
            foreach ($value as $key2 => $info) {
                $str = $str . "    $key2 : $info\n";
            }
        }
        $this->message = 'Hi ' . $_SESSION['profil']['pseudo'] . ",

            Here is all the datas we have about you. Thank you for your trust!
    
    $str

            ---------------
            This is an automatic mail, thx to not reply.";

        return $this->sendMail();
    }

    public function sendDeleteMail2($pseudo, $mail)
    {
        $this->dest = $mail;
        $this->subject = 'Account successfully deleted  on ' . $_SERVER['SERVER_NAME'];
        $this->message = "Hi $pseudo,

            Last mail from us. We confirm that your account doesn't exist anymore.

            Have a good day!

        GG, 

            ---------------
            This is an automatic mail, thx to not reply.";

        return $this->sendMail();
    }

    public function reportMail($id)
    {
        $this->subject = 'User report on ' . $_SERVER['SERVER_NAME'];
        $this->message = "Bonjour maître des 7 océans numériques,

    L'utilisateur " . $_SESSION['id'] . " a dénoncé l'utlisateur $id comme étant un faux compte. Veuillez mettre en place les actions adéquates.

    Glorieuse journée à vous!

            Votre dévoué, " . $_SERVER['SERVER_NAME'];

        return $this->sendMail($_SESSION['profil']['email']);
    }

    public function contactMe($msg, $mail)
    {
        if (isset($_SESSION['profil'])) {
            $user = $_SESSION['profil']['pseudo'];
        } else {
            $user = 'anonymous';
        }
        $this->subject = 'User contact from ' . $_SERVER['SERVER_NAME'];
        $this->message = "Bonjour maître des 7 océans numériques,

    On vous a laissé ce messsage:

         $msg

    Glorieuse journée à vous!

            Votre dévoué, " . $_SERVER['SERVER_NAME'];

        return $this->sendMail($mail, $user);
    }

    private function linkGen($user, $action)
    {
        return 'http://' . $_SERVER['SERVER_NAME'] . self::PORT . "/validation?action=$action&token=" . rawurlencode($user['token']) . '&id=' . $user['id'];
    }
}
