<?php

namespace App\Lib;

/**
 * managing mail sending
 */
class MailSender
{
    const USER = 'jgroc-de';
    const EXP = 'jgroc2s@free.fr';
    const PASS = '';
    const PORT = ':8080';

    /**
     * @param string $dest    email address
     * @param string $subject 
     * @param string $message 
     *
     * @return string for success or failure
     */
    public function sendMail(string $dest, string $subject, string $message, string $exp = SELF::EXP)
    {
        $mail =  new \PHPMailer\PHPMailer\PHPMailer(true);
        //$mail = $this->PHPMailer;

        //$mail->IsSMTP();
        $mail->CharSet = 'UTF-8';
        //$mail->Host = "smtp.free.fr";
        $mail->SMTPDebug = 0;
        //$mail->SMTPAuth = true;
        //$mail->SMTPSecure = 'ssl';
        //$mail->Port = 465;
        $mail->Port = 25;
        //$mail->Username = $this->exp;
        //$mail->Password = $this->pass;
        $mail->setFrom($exp, self::USER);
        $mail->addAddress($dest);
        $mail->Subject = $subject;
        $mail->Body = $message;
        return $mail->send();
    }

    /**
     * @param string $login name
     * @param string $dest  email address
     * @param string $token hashed key
     */
    public function sendValidationMail(string $login, string $dest, string $token)
    {
        $subject = 'Matcha Activation link';
        $message = 'Hi ' . $login .',

            Welcome on match a Rick&Morty!
            To activate your account, plz click on the link bellow or paste it into your web browser.

            http://' . $_SERVER['SERVER_NAME'] . self::PORT .'/validation?action=activation&login=' . urlencode($login) . '&token=' . urlencode($token) . '

            ---------------
            This is an automatic mail, thx to not reply.';
        return $this->sendMail($dest, $subject, $message);
    }

    /**
     * @param string $login name
     * @param string $dest  email address
     * @param string $token hashed key
     */
    public function sendResetMail(string $login, string $dest, string $token)
    {
        $subject = 'Matcha Reinitialisation link';
        $message = 'Hi ' . $login .',

            A password reinitialistion request has been made on our website.
            To proceed, plz click on the link bellow or paste it into your web browser.

            http://' . $_SERVER['SERVER_NAME'] . self::PORT . '/validation?action=reinit&login=' . urlencode($login) . '&token=' . urlencode($token) . '

            ---------------
            This is an automatic mail, thx to not reply.';
        return $this->sendMail($dest, $subject, $message);
    }
    
    /**
     * @param array $data
     */
    public function sendDataMail(array $data)
    {
        $subject = 'Your Datas on ' . $_SERVER['SERVER_NAME'];
        $str = "";
        foreach ($data as $key => $value)
        {
            $str = $str . "$key : $value\n";
        }
        $message = 'Hi ' . $_SESSION['profil']['pseudo'] . ',

            Here is all the datas we have about you. Thank you for your trust!
    
' . $str . '

            ---------------
            This is an automatic mail, thx to not reply.';
        return $this->sendMail($_SESSION['profil']['email'], $subject, $message);
    }
    
    public function sendDeleteMail()
    {
        $subject = 'Delete Request for your account on ' . $_SERVER['SERVER_NAME'];
        $message = 'Hi ' . $_SESSION['profil']['pseudo'] . ',

            If you really really want to delete your profil, please, click on the link below:
    
    http://' . $_SERVER['SERVER_NAME'] . SELF::PORT . '/validation?action=delete&token=' . $_SESSION['profil']['token'] . '

            We hope you had some good time on our website and wish you all the best!

        GG,            

            ---------------
            This is an automatic mail, thx to not reply.';
        return $this->sendMail($_SESSION['profil']['email'], $subject, $message);
    }

    public function sendDeleteMail2($pseudo, $mail)
    {
        $subject = 'Account successfully deleted  on ' . $_SERVER['SERVER_NAME'];
        $message = 'Hi ' . $pseudo . ",

            Last mail from us. We confirm that your account doesn't exist anymore.

            Have a good day!

        GG, 

            ---------------
            This is an automatic mail, thx to not reply.";
        return $this->sendMail($mail, $subject, $message);
    }
    
    public function reportMail($id)
    {
        $subject = 'User report on ' . $_SERVER['SERVER_NAME'];
        $message = "Bonjour maître des 7 océans numériques,

    L'utilisateur " . $_SESSION['id'] . " a dénoncé l'utlisateur " . $id . " comme étant un faux compte. Veuillez mettre en place les actions adéquates.

    Glorieuse journée à vous!

            Votre dévoué, " . $_SERVER['SERVER_NAME'];
        return $this->sendMail(SELF::EXP, $subject, $message, $_SESSION['profil']['email']);
    }
}
