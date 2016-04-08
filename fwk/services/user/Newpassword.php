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
class Newpassword extends \fwk\classes\Service {

    /**
     * Oublie de mot de passe
     */
    public function executerService() {
        $user = new User(HRequete::getPOSTObligatoire('id'));
        $token = HRequete::getPOSTObligatoire('token');
        $user->checkToken($token);
        $password = HRequete::getPOSTObligatoire('user_password');
        $password2 = HRequete::getPOSTObligatoire('user_password2');
        if ($password !== $password2) {
            throw new FwkException("Veuillez saisir deux fois le même mot de passe !");
        }
        $user->setPassword($password);
        $user->merger();
        $this->setMessage('Votre mot de passe a bien été modifié !');
    }

    /**
     * Le service n'est pas sécurisé
     * @return boolean
     */
    public function isSecurised() {
        return FALSE;
    }
}
