<?php

namespace App\Controllers;

class MailController
{
    /**
     * @param $login string
     * @param $dest string mail
     * @param $token string hash key
     * @return string for success or failure
     */
    public function sendValidationMail($login, $dest, $token)
    {
        $subject = 'Matcha Activation link';
        $header = 'From: jgroc2s@free.fr';
        $message = 'Bonjour ' . $login .',

            Bienvenue sur Matcha!
            Pour activer votre compte, veuillez cliquer sur le lien ci dessous
            ou copier/coller celui-ci dans votre navigateur internet.

            http://localhost:8100/validation?action=activation&login=' . urlencode($login) . '&key=' . urlencode($token) . '

            ---------------
            Ceci est un mail automatique, Merci de ne pas y répondre.';
        if (mail($dest, $subject, $message, $header))
            return 'Registration Success! A validation mail has been sent';
        else
            return 'Registration Success! but mail not sent…';
    }

    public function sendReInitMail($login, $dest, $token)
    {
        $subject = 'Matcha Reinitialisation link';
        $header = 'From: jgroc2s@free.fr';
        $message = 'Bonjour ' . $login .',

            Une demande de rénitialisation de votre mot de passe a été faites sur notre site.
            Pour réinitialiser votre mot de passe, veuillez cliquer sur le lien ci dessous
            ou copier/coller celui-ci dans votre navigateur internet.

            http://localhost:8100/validation?action=reinit&login=' . urlencode($login) . '&key=' . urlencode($token) . '

            ---------------
            Ceci est un mail automatique, Merci de ne pas y répondre.';
        if (mail($dest, $subject, $message, $header))
            return 'Success! A validation mail has been sent!';
        else
            return 'Failure… Mail not sent…';
    }
}
