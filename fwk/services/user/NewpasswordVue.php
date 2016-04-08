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

/**
 * Description of NewPassword
 *
 * @author bruno
 */
class NewpasswordVue extends \fwk\classes\ServiceVue {

    /**
     * Vue
     */
    public function executerService() {
        $user = new User(HRequete::getPOSTObligatoire('id'));
        $token = HRequete::getPOSTObligatoire('token');
        $user->checkToken($token);
        echo BP::getNewPasswordForm($user);
    }

    /**
     * Titre de page
     * @return string
     */
    public function getTitre() {
        return 'Oublie de mot de passe';
    }

    /**
     * Le service n'est pas sécurisé
     * @return boolean
     */
    public function isSecurised() {
        return FALSE;
    }

    /**
     * Validation du formulaire
     * @return boolean
     */
    protected function isFormValidation() {
        return TRUE;
    }
}
