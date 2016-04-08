<?php

use fwk\utils\HLog;

/**
 * ErrorHandler qui permet de transformer les erreur php en exception
 *
 * @category   Framework
 * @package    Framework
 * @author     Jérôme LIMOUSIN <jlimousin@thalassa.fr>
 */
class ErrorHandler {

    /**
     * Méthode fournit au handler
     * @param string $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @return boolean
     * @throws Exception
     */
    public static function thalassaErrorHandler($errno, $errstr, $errfile, $errline) {
        //dans tous les cas on ecrit dans les logs d'apache
        error_log($errno . ' - ' . $errstr . ' - ' . $errfile . ' - ' . $errline);
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting
            return;
        }

        switch ($errno) {
            case E_ERROR:
            case E_USER_ERROR:
                $message = "ERREUR [$errno] $errstr\n";
                $message .="Erreur fatale à la ligne " . $errline . "du fichier" . $errfile . "\n";
                HLog::logError($message);
                break;

            case E_WARNING:
            case E_USER_WARNING:
                $message = "WARNING [$errno] $errstr\n";
                $message .="Warning à la ligne" . $errline . " du fichier " . $errfile . "\n";
                HLog::logError($message);
                break;

            case E_NOTICE:
            case E_USER_NOTICE:
                $message = "NOTICE [$errno] $errstr\n";
                $message .="Notice à la ligne " . $errline . "du fichier " . $errfile . "\n";
                HLog::logError($message);
                break;

            case E_DEPRECATED:
                $message = "DEPRECATED [$errno] $errstr\n";
                $message .="Deprecated à la ligne " . $errline . "du fichier " . $errfile . "\n";
                HLog::logError($message);
                break;

            default:
                $message = "INCONNU [$errno] $errstr\n";
                $message .="Erreur inconnue à la ligne " . $errline . " du fichier " . $errfile . "\n";
                HLog::logError($message);
                break;
        }
    }

    /**
     * Cette méthode initialise le handler
     */
    public static function set() {
        set_error_handler(array(__CLASS__, 'thalassaErrorHandler'));
    }

}

//on delanche le handler
ErrorHandler::set();
