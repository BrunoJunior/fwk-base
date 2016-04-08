<?php

spl_autoload_register(function ($class) {
    // Project name
    $conf = parse_ini_file(__DIR__ . '/conf/project.ini');
    $projectName = $conf['name'];
    // base directory for the namespace prefix
    \fwk\utils\Conf::setNonExistingConfByCode(\fwk\utils\Conf::BASE_DIR, __DIR__ . DIRECTORY_SEPARATOR);
    // Récupération du chemin avec les namespaces
    $chemin = \fwk\utils\HString::getNamespacedClassPath($class, $projectName . '\\');
    // does the class use the namespace prefix?
    if ($chemin === NULL || !file_exists($chemin)) {
        // no, move to the next registered autoloader
        // Le fichier est inconnu --> move to the next registered autoloader
        return;
    }
    require $chemin;
});