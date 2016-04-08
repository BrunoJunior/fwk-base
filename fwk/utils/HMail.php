<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace fwk\utils;

use fwk\classes\metier\User as UserBO;

/**
 * Description of HMail
 *
 * @author bruno
 */
class HMail {

    /**
     * Envoyer un email
     * @param UserBO $connectedUser
     * @param string|array $destinataires
     * @param string $sujet
     * @param string $message
     * @return boolean
     */
    public static function envoyer(UserBO $connectedUser, $destinataires, $sujet, $message) {
        $to = $destinataires;
        if (is_array($to)) {
            $to = implode(',', $to);
        }
        $subject = strip_tags($sujet);
        $headers = "From: " . strip_tags($connectedUser->email) . "\r\n";
        $headers .= "Reply-To: ". strip_tags($connectedUser->email) . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8\r\n";

        $htmlMessage = '<html><body>';
        $htmlMessage .= '<img src="http://co-voiturage.bdesprez.com/resources/img/visu.jpg" alt="Logo" style="float: left;"/>';
        $htmlMessage .= '<h1>Message de '.$connectedUser->toHtml().'</h1>';
        $htmlMessage .= '<div>'.  $message.'</div>';
        $htmlMessage .= '</body></html>';

        return mail($to, $subject, $htmlMessage, $headers);
    }

    /**
     * Obtenir un token de vérification d'email
     * @return string
     */
    public static function getToken() {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }
}
