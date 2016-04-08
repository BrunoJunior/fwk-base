<?php

require __DIR__ . '/autoload.php';
require __DIR__ . '/fwk/autoload.php';
$root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
$conf = parse_ini_file(__DIR__ . '/conf/project.ini');
// Project name
$projectName = $conf['name'];
fwk\utils\Cache::add('', 'root', $root);
fwk\utils\Cache::add('', 'project_conf', $conf);
fwk\utils\Cache::add('', 'project_name', $projectName);
$service = fwk\utils\HRequete::getGET('service');
$serviceName = "\\$projectName\\";
$page = '';
if (empty($service)) {
    $page = fwk\utils\HRequete::getGET('page', fwk\utils\HArray::getVal($conf, 'default_page', 'liste'));
    $service = $page;
    $group = fwk\utils\HRequete::getGET('pagegroup', fwk\utils\HArray::getVal($conf, 'default_group'));
    $serviceName .= "pages\\";
} else {
    $group = fwk\utils\HRequete::getGET('pagegroup');
    $serviceName .= "services\\";
}
if (!empty($group)) {
    $serviceName .= $group . "\\";
}
$serviceName .= fwk\utils\HString::camel($service);

// Recherche dans les services du fwk
if (!class_exists($serviceName)) {
    $serviceName = "\\fwk\\services\\";
    if (!empty($group)) {
        $serviceName .= $group . "\\";
    }
    $serviceName .= fwk\utils\HString::camel($service);
}

\fwk\utils\HLog::log("Service name : $serviceName");

// Toujours pas trouvé
if (!class_exists($serviceName)) {
    $serviceName = "\\fwk\\services\\Err404";
    if (!empty($page)) {
        $serviceName .= "Vue";
    }
}

$serviceInstance = new $serviceName();

// Accès sécurisé ?
if ($serviceInstance->isSecurised()) {
    session_start();
    if ($serviceInstance instanceof \fwk\classes\ServiceVue &&
            !\fwk\utils\HSession::getUser()->existe()) {
        $serviceInstance = new fwk\services\user\LoginVue();
    }
}
fwk\utils\HRequete::setGetToPost();
\fwk\utils\Cache::add('', 'service', $serviceInstance);

if (!($serviceInstance instanceof \fwk\classes\ServiceVue)) {
    header('Content-Type: application/json');
}
$serviceInstance->executer();
