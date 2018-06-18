<?php

namespace App\Lib;

/**
 * managing mail sending
 */
class MailSender extends \App\Constructor
{
    const USER = 'jgroc-de';
    const EXP = 'lol@lol.com';
    const PASS = '';
    const URL = 'http://localhost:8100';

    /**
     * @param string $dest    email address
     * @param string $subject 
     * @param string $message 
     *
     * @return string for success or failure
     */
    public function sendMail(string $dest, string $subject, string $message)
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
        $mail->setFrom(self::EXP, self::USER);
        $mail->addAddress($dest);
        $mail->Subject = $subject;
        $mail->Body = $message;
        //if ($mail->send())
        if (false)
            return 'Registration Success! A validation mail has been sent';
        else
            return 'Registration Success! but mail not sent…';
    }

    /**
     * @param string $login name
     * @param string $dest  email address
     * @param string $token hashed key
     */
    public function sendValidationMail(string $login, string $dest, string $token)
    {
        $subject = 'Matcha Activation link';
        $url = 'localhost:8100';
        $message = 'Hi ' . $login .',

            Welcome on match a Rick&Morty!
            To activate your account, plz click on the link bellow or paste it into your web browser.

            ' . self::URL . '/validation?action=activation&login=' . urlencode($login) . '&key=' . urlencode($token) . '

            ---------------
            This is an automatic mail, thx to not reply.';
        $this->sendMail($dest, $subject, $message);
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

            ' . self::URL . '/validation?action=reinit&login=' . urlencode($login) . '&key=' . urlencode($token) . '

            ---------------
            This is an automatic mail, thx to not reply.';
        $this->sendMail($dest, $subject, $message);
    }
}
