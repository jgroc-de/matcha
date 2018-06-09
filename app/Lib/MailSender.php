<?php

namespace App\Lib;

class MailSender extends \App\Constructor
{
    const USER = 'jgroc-de';
    const EXP = 'lol@lol.com';
    const PASS = '';
    const URL = 'http://localhost:8100';
    /**
     * @param $dest string mail
     * @param $subject string mail
     * @param $message string mail
     * @param $header string mail
     * @return string for success or failure
     */
    public function sendMail($dest, $subject, $message)
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
        if ($mail->send())
            return 'Registration Success! A validation mail has been sent';
        else
            return 'Registration Success! but mail not sent…';
    }

    /**
     * @param $login string
     * @param $dest string mail
     * @param $token string hash key
     */
    public function sendValidationMail($login, $dest, $token)
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
     * @param $login string
     * @param $dest string mail
     * @param $token string hash key
     */
    public function sendResetMail($login, $dest, $token)
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
