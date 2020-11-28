<?php


namespace App\Lib\Mail;


Interface MailInterface
{
    const TYPE_TEXT = 'text/plain';
    const TYPE_HTML = 'text/html';
    const OWNER = 'Matcha webmasta';
    const MAIL = 'jgroc-de@student.42.fr';

    public function setFrom(string $email, string $name): MailInterface;
    public function setSubject(string $subject): MailInterface;
    public function addTo(string $email, string $name): MailInterface;
    public function setReplyTo(string $email, string $name): MailInterface;
    public function addContent(string $type, string $message): MailInterface;
    public function addAttachment(string $file): MailInterface;
    public function send(string $replyTo = self::MAIL, string $name = self::OWNER): bool;
}