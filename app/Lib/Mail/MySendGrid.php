<?php


namespace App\Lib\Mail;

use SendGrid\Mail\Mail;

class MySendGrid implements MailInterface
{
    /** @var Mail */
    private $mail;

    public function __construct()
    {
        $this->mail = new Mail();
    }

    public function setFrom(string $email, string $name): MailInterface
    {
        $this->mail->setFrom($email, $name);

        return $this;
    }

    public function setSubject(string $subject): MailInterface
    {
        $this->mail->setSubject($subject);

        return $this;
    }

    public function addTo(string $email, string $name): MailInterface
    {
        $this->mail->addTo($email, $name);

        return $this;
    }

    public function setReplyTo(string $email, string $name): MailInterface
    {
        $this->mail->setReplyTo($email, $name);

        return $this;
    }

    public function addContent(string $type, string $message): MailInterface
    {
        $this->mail->addContent($type, $message);

        return $this;
    }

    public function addAttachment(string $file): MailInterface
    {
        $this->mail->addAttachment($file);

        return $this;
    }

    public function send(string $replyTo = self::MAIL, string $name = self::OWNER): bool
    {
        $this->mail->setFrom(MailInterface::MAIL, MailInterface::OWNER);
        $this->mail->setReplyTo($replyTo, $name);
        $sendgrid = new \SendGrid($_ENV['SENDGRID_API_KEY']);
        try {
            $response = $sendgrid->send($this->mail);
            return true;
        } catch (\Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
            return false;
        }
    }
}