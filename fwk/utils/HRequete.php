<?php

/**
 * Esope fichier source
 * @category   Framework
 * @package    Framework
 * @author     Jérôme LIMOUSIN <jlimousin@thalassa.fr>
 */

namespace fwk\utils;

use Exception;

/**
 * Helper de manipulation des requetes HTTP
 * @category   Framework
 * @package    Framework
 * @subpackage Helper
 * @author     Jérôme LIMOUSIN <jlimousin@thalassa.fr>
 */
class HRequete {

    /**
     * Stocke les retours possible pour le service
     * @var array
     */
    private static $retours = array();

    const RETOUR_RESULTAT = 'resultat';
    const RETOUR_MESSAGE = 'message';
    const NAGIVATEUR_ONGLET = "esope_navigateur_onglet_id";
    const SESSION_KEY = "esope_session_key";
    const EMETTEUR_LICENCE = "emetteur_licence";
    const EMETTEUR_VENDEUR = "emetteur_vendeur";
    const EMETTEUR_CAISSE = "emetteur_caisse";
    const DATA_TYPE = "esope_data_type";
    const SUFFIXE_OLD_VALUE = "-old";
    const DATA_TYPE_HTML = "html";
    const DATA_TYPE_JSON = "json";
    const DATA_TYPE_JSON_REST = "json_rest";
    const DATA_TYPE_TXT = "txt";

    /**
     * Recupere une valeur poster si elle est présente
     * @param string $param
     * @param string $defaut
     * @return string
     */
    public static function getPOST($param, $defaut = "") {
        $valeur = HArray::getVal($_POST, $param, $defaut);
        if ($valeur == NULL) {
            return $valeur;
        }
        return trim($valeur);
    }

    /**
     * Recupere un parametre du post et controle qu'il est bien valorisé
     * @param string $param
     * @throws Exception
     * @return mixed
     */
    public static function getPOSTObligatoire($param) {
        $valeur = static::getPOST($param);
        if ($valeur == "") {
            HLog::logError("Paramètre obligatoire non valorisé : " . $param);
            throw new Exception("Execution impossible, paramètre manquant.");
        }
        return $valeur;
    }

    /**
     * Recupere une valeur getter si elle est présente
     * @param string $param
     * @param string $defaut
     * @return string
     */
    public static function getGET($param, $defaut = "") {
        $valeur = HArray::getVal($_GET, $param, $defaut);
        if ($valeur == NULL) {
            return $valeur;
        }
        return trim($valeur);
    }

    /**
     * Cette méthode récupere une valeur du post et si elle n'est pas présente la prends dan sle get
     * @param string $param
     * @return mixed
     */
    public static function getPOSTGET($param) {
        $value = self::getPOST($param);
        if ($value == "") {
            $value = self::getGET($param);
        }
        return $value;
    }

    /**
     * Cette méthode vide le tableau POST
     */
    public static function viderPOST() {
        $_POST = array();
    }

    /**
     * Mets a jour une valeur dans le tableau global $_POST
     * @param string $cle
     * @param string $valeur
     */
    public static function setPOST($cle, $valeur) {
        $_POST[$cle] = $valeur;
    }

    /**
     * Récupere un parametre du post et le retourne au format float
     * sinon NULL
     * @param string $param nom du parametre a recuperer
     */
    public static function getPOSTFloat($param) {
        $valeur = self::getPOST($param);
        return filter_var($valeur, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
    }

    /**
     * Récupere un parametre du post et le retourne au format integer
     * sinon NULL
     * @param string $param nom du parametre a recuperer
     */
    public static function getPOSTInteger($param) {
        $valeur = self::getPOST($param);
        return filter_var($valeur, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
    }

    /**
     * Récupere un parametre du post et le retourne au format Boolean
     * sinon NULL
     * @param type $param
     */
    public static function getPOSTBool($param) {
        $valeur = self::getPOST($param, NULL);
        if (!isset($valeur)) {
            return NULL;
        }
        if (($valeur === 1) || ($valeur === '1') || ($valeur === TRUE) || ($valeur === 'true') || ($valeur === 'TRUE') || ($valeur === 'on') || ($valeur === 'ON') || ($valeur === 'yes') || ($valeur === 'YES')) {
            return TRUE;
        } elseif (($valeur === 0) || ($valeur === '0') || ($valeur === FALSE) || ($valeur === 'false') || ($valeur === 'FALSE') || ($valeur === 'off') || ($valeur === 'OFF') || ($valeur === 'no') || ($valeur === 'NO')) {
            return FALSE;
        }
        return NULL;

        // pb avec un paramètre bool = 0 : il retourne NULL
        // return filter_var($valeur, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    /**
     * Recupere une valeur poster et retourne un objet
     * @param string $param
     * @param string $defaut
     * @return object
     */
    public static function getPOSTObjet($param, $defaut = "") {
        return HArray::getVal($_POST, $param, $defaut);
    }

    /**
     * Retourne l'identifiant de l'onglet du navigateur
     * @return string
     */
    public static function getNavigateurOngletId() {
        return static::getPOSTGET(static::NAGIVATEUR_ONGLET);
    }

    /**
     * Retourne vrai si le parametre est présent dans le post
     * @param string $param
     * @return boolean
     */
    public static function isParamPostPresent($param) {
        return array_key_exists($param, $_POST);
    }

    /**
     * Retourne vrai si le parametre est présent dans le get
     * @param string $param
     * @return boolean
     */
    public static function isParamGetPresent($param) {
        return array_key_exists($param, $_GET);
    }

    /**
     * Retourne le contenu d'un fichier issue du post
     * @param string $nomFichier
     * @return string
     */
    public static function getFile($nomFichier) {
        if (isset($_FILES[$nomFichier]['tmp_name'])) {
            $tmp_file = $_FILES[$nomFichier]['tmp_name'];
            if (is_uploaded_file($tmp_file)) {
                return $tmp_file;
            }
        }
        return "";
    }

    /**
     * Retourne le nom d'un fichier issue du post
     * @param string $nomFichier
     * @return strinf
     */
    public static function getFileName($nomFichier) {
        if (isset($_FILES[$nomFichier]['name'])) {
            return $_FILES[$nomFichier]['name'];
        }
        return "";
    }

    /**
     * Deverse tous les parametres du $_GET dans $_POST
     */
    public static function setGetToPost() {
        $_POST = array_merge($_GET, $_POST);
    }

    /**
     * Ajout un retour negatif au service avec potentiellmeent un message
     * @param string $message
     */
    public static function ajouterRetourKo($message = "") {
        static::$retours[static::RETOUR_RESULTAT] = FALSE;
        if ($message != "") {
            static::$retours[static::RETOUR_MESSAGE] = $message;
        }
    }

    /**
     * Retourne les retours du service
     * @return array
     */
    public static function getRetours() {
        return static::$retours;
    }

    /**
     * Retourne vrai si la requete est JSON
     * @return boolean
     */
    public static function isRequeteJSON() {
        //par defaut si c'est vide on considere que c'est du json
        return (static::getPOST(HRequete::DATA_TYPE) == "" || static::DATA_TYPE_JSON == static::getPOST(HRequete::DATA_TYPE));
    }

    /**
     * Retourne vrai si la requete est TXT
     * @return boolean
     */
    public static function isRequeteTXT() {
        return (static::DATA_TYPE_TXT == static::getPOST(HRequete::DATA_TYPE));
    }

    /**
     * Redirige une requete post
     * @param string $url URL.
     * @param array $params POST data. Example: array('foo' => 'var', 'id' => 123)
     */
    public static function redirectPost($url, array $params = array()) {
        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'timeout' => 20.0,
                'content' => http_build_query($params)
            )
        );
        $context = stream_context_create($opts);
        //Utilisation du contexte dans l'appel
        $fp = fopen($url, 'rb', false, $context);
        if ($fp) {
            echo stream_get_contents($fp);
            exit();
        } else {
            $objRetour = new stdClass();
            $objRetour->resultat = new stdClass();
            $objRetour->resultat->erreur = 1;
            $objRetour->resultat->message = array("Erreur redirection !");
            $objRetour->reponse = new stdClass();
            echo json_encode($objRetour);
        }
    }

    /**
     * Retourne un tableau des clés présentes dans le post commençant par ...
     * @param string chaine de début
     */
    public static function getListeClePostCommencant($debut) {
        $tableauResultat = array();
        foreach ($_POST as $key => $value) {
            if (HString::commencePar($key, $debut) && !HString::terminePar($key, static::SUFFIXE_OLD_VALUE)) {
                $tableauResultat[] = $key;
            }
        }
        return $tableauResultat;
    }

}
