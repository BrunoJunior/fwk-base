<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace fwk\services\user;

// BO
use fwk\classes\User;
// Helper
use fwk\utils\HRequete;
use fwk\erreurs\FwkException;

/**
 * Description of ForgotPwd
 *
 * @author bruno
 */
class Forgotpwd extends \fwk\classes\Service {

    /**
     * Oublie de mot de passe
     */
    public function executerService() {
        $email = HRequete::getPOSTObligatoire('email');
        $user = User::chargerParEmail($email);
        if (!$user->existe()) {
            throw new FwkException('Adresse e-mail inconnue !');
        }
        $isOk = $user->contacter('Oubli de mot de passe',
            '<p>Vous avez oublié votre mot de passe !</p>
            <p>Pas de panique, cliquez sur le lien ci-dessous pour en renseigner un nouveau !</p>
            <p><a href="'.Newpassword::getUrl($user->id, ['token'=>$user->getNewToken()]).'">Obtenir un nouveau mot de passe</a></p>');
        if (!$isOk) {
            throw new FwkException('Erreur lors de l\'envoi du message !');
        }
        $this->setMessage('Un email pour réinitialiser votre mot de passe vous a été envoyé !');
    }

    /**
     * Le service n'est pas sécurisé
     * @return boolean
     */
    public function isSecurised() {
        return FALSE;
    }
}
