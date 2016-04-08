<?php

/**
 * 2003-2014 XL Soft
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    XL Soft <contact@xlsoft.fr>
 *  @copyright 2014 XL Soft
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of XL Soft
 */

namespace fwk\classes;

use fwk\utils\HLog;

/**
 * Description of ClasseSimple
 *
 * @author bruno
 */
class ClasseSimple {

    /**
     * Rempli l'attribut avec la valeur passée en paramètre
     * A surcharger si l'attribut \stdClass n'est pas le même que l'attribut de l'objet
     * @param string $attribut
     * @param mixed $valeur
     */
    public function chargerAttrFromStdClassAttr($attribut, $valeur) {
        if (property_exists($this, $attribut))
            $this->$attribut = $valeur;
    }

    /**
     * Rempli l'objet à partir d'une \stdClass
     * @param \stdClass $std_class
     */
    public function chargerFromStdClass($std_class) {
        HLog::log('Debut chargerFromStdClass ' . json_encode($std_class), '', true, HLog::DEBUG);
        $attributs_std = get_object_vars($std_class);

        foreach ($attributs_std as $attribut => $valeur)
            $this->chargerAttrFromStdClassAttr($attribut, $valeur);

        HLog::log('Fin chargerFromStdClass ' . json_encode($this), '', true, HLog::DEBUG);
    }
}
