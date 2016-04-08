<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace fwk\services;

use fwk\classes\Service;

/**
 * Description of Install
 *
 * @author bruno
 */
class Install extends Service {

    /**
     * Installation
     */
    public function executerService() {
        return \fwk\classes\User::install();
    }

    public function isConnexionObligatoire() {
        return FALSE;
    }

}
