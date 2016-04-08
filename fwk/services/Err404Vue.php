<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace fwk\services;

/**
 * Description of 404
 *
 * @author bruno
 */
class Err404Vue extends \fwk\classes\ServiceVue {

    /**
     * Page 404
     */
    public function executerService() {
        echo '<div id="404" class="bg-danger text-center"><h3>Page inconnue !</h3></div>';
    }

    /**
     * Titre de la page
     * @return string
     */
    public function getTitre() {
        return 'Erreur 404';
    }

    /**
     * La page n'est pas sécurisé
     * @return boolean
     */
    public function isSecurised() {
        return FALSE;
    }
}
