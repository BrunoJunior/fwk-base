<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace fwk\services\user;

use fwk\classes\UserBP;

/**
 * Description of Inscription
 *
 * @author bruno
 */
class InscriptionVue extends \fwk\classes\ServiceVue {

    /**
     * Vue formulaire connexion
     */
    public function executerService() {
        echo UserBP::getInscriptionForm();
    }

    public function getTitre() {
        return 'Inscription';
    }

    public function isSecurised() {
        return FALSE;
    }
    
    protected function isFormValidation() {
        return TRUE;
    }

}
