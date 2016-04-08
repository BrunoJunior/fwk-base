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

use \Exception;

/**
 * Permet de générer des comptes rendus d'exécution des tâches lancées
 *
 * @author bruno
 */
class HLog {

    const DEBUG = 0;
    const INFO = 1;
    const WARNING = 2;
    const ERROR = 3;

    private static $level;
    private static $dftLevel;

    private static function getStrLevel($level) {
        switch ($level) {
            case static::DEBUG:
                return 'DEBUG';
            case static::INFO:
                return 'INFO';
            case static::WARNING:
                return 'WARNING';
            case static::ERROR:
                return 'ERROR';
        }
    }

    /**
     * Définir le niveau de log par programmation
     * @param int $level
     */
    public static function setLevel($level) {
        if (empty(static::$dftLevel)) {
            static::$dftLevel = static::getLevel();
        }
        static::$level = $level;
    }

    /**
     * Remise du niveau à sa valeur par défaut
     */
    public static function resetDefaultLevel() {
        static::$level = static::$dftLevel;
    }

    /**
     * Obtenir le niveau de log
     * @return int
     */
    private static function getLevel() {
        if (empty(static::$level)) {
            static::$level = Conf::getIniVal(Conf::LOG_LEVEL, 3);
        }
        return static::$level;
    }

    /**
     * Loggueur
     * @param string $message
     * @param string $filename
     * @param boolean $is_date
     * @param int $level
     */
    public static function log($message, $filename = '', $is_date = true, $level = 0) {
        if ($level >= static::getLevel()) {
            if (!empty($filename) && substr($filename, -4) == '.txt')
                $filename = substr($filename, 0, -4);
            if ($is_date)
                $filename .= date('Ymd');
            error_log(static::getStrLevel($level) . ' - [' . date('Y-m-d H:i:s') . ']' . $message . PHP_EOL, 3, dirname(dirname(__DIR__)) . '/logs/' . $filename . '.txt');
        }
    }

    /**
     * Loggueur d'erreur
     * @param string $message
     * @param string $filename
     * @param boolean $is_date
     */
    public static function logError($message, $filename = '', $is_date = true) {
        static::log($message, $filename, $is_date, static::ERROR);
    }

    /**
     * Loggueur d'exception
     * @param Exception $exc
     * @param string $filename
     * @param boolean $is_date
     */
    public static function logException(Exception $exc, $filename = '', $is_date = true) {
        static::log($exc->getMessage(), $filename, $is_date, static::ERROR);
        static::log($exc->getTraceAsString(), $filename, $is_date, static::ERROR);
    }

    /**
     * Loggueur de warning
     * @param string $message
     * @param string $filename
     * @param boolean $is_date
     */
    public static function logWarning($message, $filename = '', $is_date = true) {
        static::log($message, $filename, $is_date, static::WARNING);
    }

    /**
     * Loggueur d'info
     * @param string $message
     * @param string $filename
     * @param boolean $is_date
     */
    public static function logInfo($message, $filename = '', $is_date = true) {
        static::log($message, $filename, $is_date, static::INFO);
    }

}
