<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace fwk\services;

use fwk\classes\Service;

/**
 * Description of Test
 *
 * @author bruno
 */
class Test extends Service {
    /**
     * Service de test
     */
    public function executerService() {
        $this->addResponseItem('array', ['array']);
        $obj = new \stdClass();
        $obj->objet = 'objet';
        $this->addResponseItem('object', $obj);
        $this->addResponseItem('String', 'My value');
        $this->addResponseItem('Integer', 42);
        $this->addResponseItem('Float', 42.24);
    }
}
