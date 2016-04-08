<?php
require __DIR__ . '/erreurs/ErrorHandler.php';
require __DIR__ . '/erreurs/EndHandler.php';
require __DIR__ . '/utils/Conf.php';
require __DIR__ . '/utils/HString.php';
require __DIR__ . '/lib/password.php';


spl_autoload_register(function ($class) {
    // base directory for the namespace prefix
    \fwk\utils\Conf::setNonExistingConfByCode(\fwk\utils\Conf::BASE_DIR, __DIR__ . DIRECTORY_SEPARATOR);
    // Récupération du chemin avec les namespaces
    $chemin = \fwk\utils\HString::getNamespacedClassPath($class, 'fwk\\', FALSE);
    // does the class use the namespace prefix?
    if ($chemin === NULL || !file_exists($chemin)) {
        // no, move to the next registered autoloader
        // Le fichier est inconnu --> move to the next registered autoloader
        return;
    }
    require $chemin;
});