<?php

use fwk\utils\HLog;

/**
 * Méthode appelé a chaque finde script
 */
function handleShutdown() {
    $error = error_get_last();
    if ($error !== NULL) {
        //il y a eu une FATAL ERROR on essaie de faire le rollback
        $info = "[Error] trapée dans handleShutdown file:" . $error['file'] . " | ln:" . $error['line'] . " | msg:" . $error['message'] . PHP_EOL;
        HLog::logError($info);
        HLog::logInfo(json_encode($_POST));
    }
}

//défini la méthode utilisé en fin de script
register_shutdown_function('handleShutdown');
