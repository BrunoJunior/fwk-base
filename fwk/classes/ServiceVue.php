<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace fwk\classes;

/**
 * Helpers
 */
use fwk\utils\HDatabase;
use fwk\utils\HLog;
use fwk\utils\Cache;
use fwk\utils\Html;
use fwk\utils\HArray;
/**
 * Services
 */
use fwk\services\user\Logout;
use fwk\services\user\EditVue;

/**
 * Description of ServiceVue
 *
 * @author bruno
 */
abstract class ServiceVue extends Service {

    /**
     * Liste des fichiers js liés
     * @var array
     */
    private $jsFiles = [];

    /**
     * Liste des fichiers css liés
     * @var type
     */
    private $cssFiles = [];

    /**
     * Titre du service de vue
     * @var string
     */
    private $titre;

    /**
     * Ce service doit-il être chargé complètement
     * (page complète)
     * @var boolean
     */
    private $complete = TRUE;

    /**
     * @return string Titre de la page
     */
    public abstract function getTitre();

    /**
     * Setter titre de la page
     * @param type $titre
     */
    public function setTitre($titre) {
        $this->titre = $titre;
    }

    /**
     * La vue doit-elle être chargée complètement
     * @return boolean
     */
    public function isComplete() {
        return TRUE;
    }

    /**
     * Gestion de l'exécution d'un service de vue
     * @param string $titre
     */
    public function executer($titre = NULL) {
        $this->avantExecuterService();
        if ($titre === NULL) {
            $titre = $this->getTitre();
        }
        $this->complete = $this->isComplete();
        $this->setTitre($titre);
        $this->addJs($this->getName());
        $this->addCss($this->getName());
        ob_start();
        $err = FALSE;
        if ($this->complete) {
            HDatabase::openTransaction();
        }
        try {
            if ($this->complete) {
                $this->afficher();
            } else {
                $this->executerService();
            }
            echo ob_get_clean();
        } catch (\Exception $exc) {
            $err = TRUE;
            HLog::logError($exc->getMessage());
            HLog::logError($exc->getTraceAsString());
            echo '<div class="alert alert-danger" role="alert">';
            echo $exc->getMessage();
            echo '</div>';
        }
        if ($this->complete) {
            HDatabase::closeTransaction($err);
        }
        $this->insertJs();
        $this->insertCss();
        $this->apresExecuterService($err);
    }

    /**
     * Afficher la vue complète
     */
    private function afficher() {
        $root = Cache::get('', 'root');
        $conf = Cache::get('', 'project_conf');
        $user = $this->getUser();
        echo '
<!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
            <title>' . HArray::getVal($conf, 'title') . '</title>
            <link rel="stylesheet" href="' . $root . 'fwk/lib/font-awesome/css/font-awesome.min.css">
            <link rel="stylesheet" href="' . $root . 'fwk/lib/bootstrap/css/bootstrap.min.css">
            <link rel="stylesheet" href="' . $root . 'fwk/resources/css/global.css">
            <link rel="stylesheet" href="' . $root . 'fwk/lib/jquery-ui/jquery-ui.min.css">
        </head>
        <body>
            <script type="text/javascript" src="' . $root . 'fwk/lib/jquery/jquery-2.1.4.min.js"></script>
            <script type="text/javascript" src="' . $root . 'fwk/lib/jquery-ui/jquery-ui.min.js"></script>
            <div id="loading" style="display: none;"><p>' . Html::getIcon('cog', 'fa-spin') . '</p><p>Chargement en cours ...</p></div>
            <header class="navbar navbar-static-top bs-docs-nav" id="top" role="banner">
                <div class="container"><a href="/" class="hidden-xs">
                        <img id="logo" src="' . $root . 'resources/img/logo.jpg" class="img-responsive img-thumbnail pull-left" alt="Logo" />
                    </a>';
        echo '<h1 class="text-center"><span class="label label-default">' . HArray::getVal($conf, 'title') . '</span></h1>';
        echo '<a href="/" class="btn btn-primary visible-xs home" role="button" data-toggle="tooltip" title="Accueil">' . Html::getIcon('home') . '</a>';
        if (!empty($user) && $user->existe()) {
            echo '<button id="cov-deco" class="btn btn-danger deconnexion" url="' . Logout::getUrl() . '" role="button" data-toggle="tooltip" title="Déconnexion" data-confirm="Êtes-vous sûr ?">' . Html::getIcon('sign-out') . '</button>';
            echo '<a class="btn btn-primary account" href="' . EditVue::getUrl($user->id) . '" role="button" data-toggle="tooltip" title="Mes infos">' . Html::getIcon('user') . '</a>';
        }
        echo '<a class="btn btn-primary doc" href="/documentation.html" role="button" data-toggle="tooltip" title="Documentation">' . Html::getIcon('book') . '</a>';
        echo '      <hr />
                    <h3 class="text-center"><span class="label label-info">' . $this->titre . '</span></h3>
                </div>
            </header>
            <div id="main-page" class="container container-alert">
            <div id="cov-alert-error" class="alert alert-danger hidden cov-alert-error" role="alert">
                <span class="message"></span>
            </div>
            <div id="cov-alert-success" class="alert alert-success hidden cov-alert-success" role="alert">
                <span class="message"></span>
            </div>';
        $this->executerService();
        echo '</div>
                <script src="' . $root . 'fwk/lib/bootstrap/js/bootstrap.min.js"></script>
                <script src="' . $root . 'fwk/resources/js/global.js"></script>
                <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.2.8/jquery.form-validator.min.js"></script>
        </body>
    </html>';
    }

    /**
     * Ajouter un fichier javascript à la vue
     * @param string $filename
     */
    protected function addJs($filename) {
        $filePath = $this->getJsDirname() . DIRECTORY_SEPARATOR . $filename . '.js';
        HLog::log("JS file path : $filePath");
        if (file_exists($filePath)) {
            $this->jsFiles[] = $filePath;
        }
    }

    /**
     * Ajouter un ficheir css à la vue
     * @param string $filename
     */
    protected function addCss($filename) {
        $filePath = $this->getCssDirname() . DIRECTORY_SEPARATOR . $filename . '.css';
        HLog::log("CSS file path : $filePath");
        if (file_exists($filePath)) {
            $this->cssFiles[] = $filePath;
        }
    }

    /**
     * Obtenir le répertoire où sont stockés les fichiers javascript pour la vue
     * @return string
     */
    protected function getJsDirname() {
        return $this->getDirname() . DIRECTORY_SEPARATOR . 'js';
    }

    /**
     * Obtenir le répertoire où sont stockés les fichiers css pour la vue
     * @return string
     */
    protected function getCssDirname() {
        return $this->getDirname() . DIRECTORY_SEPARATOR . 'css';
    }

    /**
     * Ajout des fichiers javascript à la vue
     */
    protected function insertJs() {
        echo "\n<script type=\"text/javascript\" >\n";
        echo "$(document).ready(function(){\n";
        if ($this->isFormValidation()) {
            echo "$.validate({modules : 'html5', lang : 'fr'});";
        }
        foreach ($this->jsFiles as $file) {
            include $file;
        }
        echo "\n});";
        echo "\n</script>\n";
    }

    /**
     * Ajout des fichiers CSS à la vue
     */
    protected function insertCss() {
        foreach ($this->cssFiles as $file) {
            echo "\n<style type='text/css' media='all'>\n";
            include $file;
            echo "\n</style>\n";
        }
    }

    /**
     * L'extension d'un service de vue sera html
     * @return string
     */
    protected static function getExtension() {
        return 'html';
    }

    /**
     * Le service requiert-il la validation d'un formulaire
     * @return boolean
     */
    protected function isFormValidation() {
        return FALSE;
    }

}
