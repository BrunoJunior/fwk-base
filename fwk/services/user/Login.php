<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace fwk\services\user;

// BO
use fwk\classes\User;
// Helpers
use fwk\utils\HRequete;
use fwk\erreurs\FwkException;

/**
 * Description of Login
 *
 * @author bruno
 */
class Login extends \fwk\classes\Service {

    /**
     * Connexion
     * @throws Exception
     */
    public function executerService() {
        $email = HRequete::getPOST('user_email');
        $password = HRequete::getPOST('user_password');
        if (!User::connecter($email, $password)) {
            throw new FwkException('Identification incorrecte !');
        }
    }

    /**
     * O, se connecte, démarrage de session, mais utilisateur pas encore connecté
     * @return boolean
     */
    public function isConnexionObligatoire() {
        return FALSE;
    }
}
