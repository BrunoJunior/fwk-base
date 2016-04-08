<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace fwk\utils;

/**
 * Description of Conf
 *
 * @author bruno
 */
class Conf {

    const BASE_DIR = 'base_dir';

    const LOG_LEVEL = 'log_level';
    const DB_HOST = 'db_host';
    const DB_PORT = 'db_port';
    const DB_NAME = 'db_name';
    const DB_CHARSET = 'db_charset';
    const DB_USER = 'db_user';
    const DB_PASS = 'db_pass';

    public static function getConfByCode($code) {
        if (!array_key_exists($code, $GLOBALS)) {
            return NULL;
        }
        return $GLOBALS[$code];
    }

    public static function setNonExistingConfByCode($code, $value) {
        $exist = static::getConfByCode($code);
        if ($exist === NULL) {
            $GLOBALS[$code] = $value;
        }
    }

    public static function setConfByCode($code, $value) {
        $GLOBALS[$code] = $value;
    }
    
    public static function getIniVal($code, $valDefaut = NULL) {
        $conf = parse_ini_file(dirname(dirname(__DIR__)) . '/conf/conf.ini', TRUE);
        if ($code == 'mode') {
            $mode = $conf['DEFAULT'][$code];
            return $conf[$mode];
        }
        $modeConf = static::getIniVal('mode');
        if (!array_key_exists($code, $modeConf)) {
            return $valDefaut;
        }
        return $modeConf[$code];
    }

}
