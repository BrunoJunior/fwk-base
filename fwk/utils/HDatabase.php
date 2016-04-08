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

use DateTime;

/**
 * Description of HDatabase
 *
 * @author bruno
 */
class HDatabase {

    const FORMAT_DATE_BDD = 'Y-m-d';
    const FORMAT_DATE_AFFICHAGE = 'd/m/Y';

    /**
     * Connexion PDO
     * @var \PDO
     */
    private static $connexion;

    /**
     * Retourne l'instance
     * @return \PDO
     */
    public static function getInstance() {
        if (!self::$connexion) {
            try {
                self::$connexion = new \PDO(
                        'mysql:host=' . Conf::getIniVal(Conf::DB_HOST) .
                        ';port=' . Conf::getIniVal(Conf::DB_PORT) .
                        ';dbname=' . Conf::getIniVal(Conf::DB_NAME) .
                        ';charset=' . Conf::getIniVal(Conf::DB_CHARSET), Conf::getIniVal(Conf::DB_USER), Conf::getIniVal(Conf::DB_PASS));
            } catch (\Exception $exc) {
                HLog::logException($exc);
                die('Erreur de connexion à la BDD !');
            }
        }
        return self::$connexion;
    }

    /**
     * Débute une transaction
     * @return boolean
     */
    public static function openTransaction() {
        return HDatabase::getInstance()->beginTransaction();
    }

    /**
     * Termine une transaction
     * @param boolean $withError rollback en cas d'erreur
     * @return boolean
     */
    public static function closeTransaction($withError = FALSE) {
        if ($withError) {
            return HDatabase::getInstance()->rollBack();
        } else {
            return HDatabase::getInstance()->commit();
        }
    }

    /**
     * Lance une requête paramétrée de type SELECT
     * @param string $requete
     * @param array $params
     * @return boolean|array FALSE si une erreur survient, un tableau de résultat sinon
     */
    public static function rechercher($requete, $params) {
        $stmt = HDatabase::getInstance()->prepare($requete);
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $cle = $key;
                if (is_numeric($key)) {
                    $cle = $key + 1;
                } elseif (!HString::commencePar($cle, ':')) {
                    $cle = ':' . $key;
                }
                $stmt->bindValue($cle, $value);
            }
        }
        HLog::log(print_r($stmt, TRUE));
        HLog::log(print_r($params, TRUE));
        if ($stmt->execute()) {
            return $stmt->fetchAll();
        }
        return FALSE;
    }

    /**
     * Lance une requête paramétrée de type INSERT / UPDATE / DELETE
     * @param string $requete
     * @param array $params
     * @return boolean FALSE si une erreur survient
     */
    public static function executer($requete, $params = NULL) {
        $stmt = HDatabase::getInstance()->prepare($requete);
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $cle = $key;
                if (is_numeric($key)) {
                    $cle = $key + 1;
                } elseif (!HString::commencePar($cle, ':')) {
                    $cle = ':' . $key;
                }
                $stmt->bindValue($cle, $value);
            }
        }
        HLog::log(print_r($stmt, TRUE));
        HLog::log(print_r($params, TRUE));
        return $stmt->execute();
    }

    /**
     * Dernier id inséré en BDD
     * @return integer
     */
    public static function getDernierIdCree() {
        return HDatabase::getInstance()->lastInsertId();
    }

    /**
     * Convertir une date pour la BDD
     * @param string $date
     * @return string
     */
    public static function convertDateForBDD($date) {
        $dateBdd = DateTime::createFromFormat(static::FORMAT_DATE_AFFICHAGE, $date);
        return $dateBdd->format(static::FORMAT_DATE_BDD);
    }

    /**
     * Convertir une date depuis la BDD
     * @param string $date
     * @return string
     */
    public static function convertDateFromBDD($date) {
        return date(static::FORMAT_DATE_AFFICHAGE, strtotime($date));
    }

}
