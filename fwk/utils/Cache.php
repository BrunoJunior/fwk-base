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

namespace fwk\utils;

use \stdClass;

/**
 * Description of Cache
 *
 * @author bruno
 */
class Cache {

    private static $cache_key = array();
    private static $cache = array();
    private static $max_elements = 1000;

    /**
     * Changer le nombre maximum d'élément dans le cache
     * Si le cache devient plus petit, on efface les éléments en trop
     * @param int $max_elements
     */
    public static function changeMaxElements($max_elements) {
        $nb_elements = count(static::$cache_key);
        while ($max_elements < $nb_elements)
            static::removeFirst();

        static::$max_elements = $max_elements;
    }

    /**
     * La clé finale est la concaténation du nom de la classe et de la clé
     * @param type $class_name
     * @param type $key
     * @return type
     */
    private static function getTotalKey($class_name, $key, $prefix = '') {
        return strtoupper(trim($class_name) . $prefix . trim($key));
    }

    /**
     * Suppression d'un élément du cache (1er)
     */
    private static function removeFirst() {
        $key_to_remove = array_shift(static::$cache_key);
        unset(static::$cache[$key_to_remove]);
    }

    /**
     * Ajout d'un élément dans le cache
     * @param string $class_name
     * @param string $key
     * @param string $value
     */
    public static function add($class_name, $key, $value, $prefix = '', $removable = true) {
        $total_key = static::getTotalKey($class_name, $key, $prefix);

        if ($removable)
            static::$cache_key[] = $total_key;

        static::$cache[$total_key] = $value;

        if (count(static::$cache_key) >= static::$max_elements)
            static::removeFirst();
    }

    /**
     * Définir les infos de l'action dans le cache
     * @param string $action
     * @param stdClass $params
     */
    public static function setInfosAction($action, $params) {
        $infos = new stdClass();
        $infos->action = $action;
        $infos->params = $params;
        static::add('SService', 'infos_action', $infos, '', false);
    }

    /**
     * Récupérer les infos de l'action du cache
     * @return stdClass (action, params)
     */
    public static function getInfosAction() {
        return static::get('SService', 'infos_action');
    }

    /**
     * Définir les paramètres actuellement en cours
     * @param stdClass $params
     */
    public static function setActualParams($class_name, $params) {
        static::add($class_name, 'params', $params, '', false);
    }

    /**
     * Récupérer les paramètres actuels
     * @return stdClass
     */
    public static function getActualParams($class_name) {
        return static::get($class_name, 'params');
    }

    /**
     * Recherche dans le cache.
     * Si la donnée est trouvée, on la positionne à la fin. Permet de conserver les éléments les plus rechercher.
     * @param string $class_name
     * @param string $key
     * @param string $prefix
     * @return mixed
     */
    public static function get($class_name, $key, $prefix = '') {
        $found = null;
        $total_key = static::getTotalKey($class_name, $key, $prefix);
        if (isset(static::$cache[$total_key])) {
            $found = static::$cache[$total_key];

            // L'élément existe dans le tableau, on le remet à la fin du tableau pour concerver les éléments les plus appelés
            array_splice(static::$cache_key, array_search($total_key, static::$cache_key) + 1, 1);
            static::add($class_name, $key, $found, $prefix);
        }

        return $found;
    }

    /**
     * Savoir si une donnée est présente dans le cache.
     * @param string $class_name
     * @param string $key
     * @param string $prefix
     * @return boolean
     */
    public static function contains($class_name, $key, $prefix = '') {
        $total_key = static::getTotalKey($class_name, $key, $prefix);
        return isset(static::$cache[$total_key]);
    }

}
