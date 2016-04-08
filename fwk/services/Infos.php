<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace fwk\services;

/**
 * Description of Infos
 *
 * @author bruno
 */
class Infos extends \fwk\classes\ServiceVue {
    /**
     * PHP INFO
     */
    public function executerService() {
        phpinfo();
    }

    public function getTitre() {
        "INFOS";
    }

}
