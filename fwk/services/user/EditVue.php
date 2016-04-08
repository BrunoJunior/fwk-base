<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace fwk\services\user;

use fwk\classes\UserBP;
use fwk\classes\User;
// Helpers
use fwk\utils\HRequete;
use fwk\erreurs\FwkException;

/**
 * Description of Add
 *
 * @author bruno
 */
class EditVue extends \fwk\classes\ServiceVue {

    /**
     * Execution du service
     */
    public function executerService() {
        $id = HRequete::getPOST('id');
        $user = new User($id);
        $connectedUser = $this->getUser();
        if (!$connectedUser->admin && $user->id !== $connectedUser->id) {
            throw new FwkException('Vous n\'êtes pas autorisé à modifier cet utilisateur !');
        }
        echo UserBP::getForm($user);
    }

    /**
     * Titre
     * @return string
     */
    public function getTitre() {
        return 'Gestion des utilisateurs';
    }

    /**
     * Validation du formulaire
     * @return boolean
     */
    protected function isFormValidation() {
        return TRUE;
    }

}
