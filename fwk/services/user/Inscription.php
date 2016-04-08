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
 * Description of Inscription
 *
 * @author bruno
 */
class Inscription extends \fwk\classes\Service {

    /**
     * Connexion
     * @throws Exception
     */
    public function executerService() {
        $email = HRequete::getPOSTObligatoire('user_email');
        $password = HRequete::getPOSTObligatoire('user_password');
        $password2 = HRequete::getPOSTObligatoire('user_password2');
        if ($password != $password2) {
            throw new FwkException("Vérifiez le mot de passe saisi !");
        }
        $user = new User();
        $user->email = $email;
        $user->admin = FALSE;
        $user->nom = HRequete::getPOSTObligatoire('user_nom');
        $user->prenom = HRequete::getPOSTObligatoire('user_prenom');
        $user->setPassword($password);
        $user->tel = HRequete::getPOST('user_tel', NULL);
        $user->merger();
        $user->connecter($email, $password);
    }

    /**
     * La session doit démarrer, mais connexion non obligatoire
     * @return boolean
     */
    public function isConnexionObligatoire() {
        return FALSE;
    }
}
