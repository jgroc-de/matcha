<?php

namespace App\Controllers;

class MailController
{
    public function sendValidationMail($login, $dest, $token)
    {
        $subject = 'Matcha Activation link';
        $header = 'From: jgroc2s@free.fr';
        $message = 'Bienvenue sur Matcha,

            Votre login est: ' . $login . '
            Pour activer votre compte, veuillez cliquer sur le lien ci dessous
            ou copier/coller celui-ci dans votre navigateur internet.

            http://localhost:8100/validation?login=' . urlencode($login) . '&key=' . urlencode($token) . '

            ---------------
            Ceci est un mail automatique, Merci de ne pas y répondre.';
        if (mail($dest, $subject, $message, $header))
            return 'Registration Success! A validation mail has been sent';
        else
            return 'Registration Success! but mail not sent…';
    }
}
