<?php

/**
 * Esope fichier source
 * @category   Framework
 * @package    Framework
 * @author     Jérôme LIMOUSIN <jlimousin@thalassa.fr>
 */
namespace fwk\utils;

/**
 * Helper de manipulation des tableaux
 * @category   Framework
 * @package    Framework
 * @subpackage Helper
 * @author     Jérôme LIMOUSIN <jlimousin@thalassa.fr>
 */
class HArray {

    /**
     * Retourne la valeur de la cellule du tableau a l'indice donné s'il existe sinon la valeur par defaut
     * @param array $array tableau de valeurs
     * @param mixed $indice indice demandé
     * @param mixed $valeurDefaut valeur
     * @return mixed valeur à l'indice si existe sinon la valeur par défaut
     */
    public static function getVal($array, $indice, $valeurDefaut = "") {
        if ($array == NULL) {
            $array = [];
        }
        if (array_key_exists($indice, $array)) {
            return $array[$indice];
        }
        return $valeurDefaut;
    }

    /**
     * Cette méthode supprime un element du tablea par sa valeur et non son indice !
     * @param array $array
     * @param mixed $value
     * @return array
     */
    public static function supprimerElement($array, $value) {
        if ($array == NULL) {
            $array = [];
        }
        $key = array_search($value, $array);
        if ($key !== FALSE) {
            unset($array[$key]);
        }
        return $array;
    }

    /**
     * Supprime un element du tableau par sa clé
     * @param $array
     * @param $value
     * @return mixed
     */
    public static function supprimerElementParIndice($array, $value) {
        if ($array == NULL) {
            $array = [];
        }
        if (array_key_exists($value, $array)) {
            unset($array[$value]);
        }
        return $array;
    }

    /**
     * Prefixe toutes les cle d'un tablea par un prefixe
     * [client_id => 12] => [data-client_id => 12]
     * @param array $array
     * @param string $prefixe
     * @return array
     */
    public static function prefixerKeys(array $array, $prefixe) {
        $arrayResultat = array();
        foreach ($array as $key => $value) {
            $arrayResultat[$prefixe . $key] = $value;
        }
        return $arrayResultat;
    }

    /**
     * Retourne le tableau sans l'element à l'indice passé en parametre et renumerote les indices
     * @param array $array
     * @param int $indice
     * @return array
     */
    public static function supprimerIndicePlusDecalage($array, $indice) {
        if (array_key_exists($indice, $array)) {
            unset($array[$indice]);
            $arrayResultat = array();
            foreach ($array as $value) {
                $arrayResultat[] = $value;
            }
            $array = $arrayResultat;
        }
        return $array;
    }

    /**
     * Retourne la premiere clé du tableau associatif
     * @param array $tab
     * @return mixed
     */
    public static function getPremiereCle(array $tab) {
        foreach ($tab as $key => $value) {
            return $key;
        }
        return '';
    }

    /**
     * Retourne la clé suivante d'un associatif
     * @param array $tab
     * @param string $cle
     * @return mixed
     */
    public static function getCleSuivante(array $tab, $cle) {
        $cleActuelle = FALSE;
        foreach ($tab as $key => $value) {
            if ($cleActuelle) {
                return $key;
            }
            if ($cle == $key) {
                $cleActuelle = TRUE;
            }
        }
        return '';
    }

    /**
     * Ajoute une valeur à un tableau uniquement si celle-ci n'est pas vide
     * @param mixed $valeur
     * @param array $array
     * @param mixed $cle
     */
    public static function ajouterSiNonVide($valeur, &$array = array(), $cle = NULL) {
        if (!empty($valeur)) {
            if (empty($cle)) {
                $array[] = $valeur;
            } else {
                $array[$cle] = $valeur;
            }
        }
    }

    /**
     * Vérifie si au moins une clé est présente dans un tableau
     * @param array $listeCle
     * @param array $array
     * @param string $prefixeCle
     * @return boolean
     */
    public static function isUneClePresente(array $listeCle, array $array, $prefixeCle = '') {
        foreach ($listeCle as $cle) {
            // Si au moins une clé est trouvé
            if (array_key_exists($prefixeCle . $cle, $array)) {
                return TRUE;
            }
        }

        // Si aucune clé n'a été trouvée
        return FALSE;
    }

    /**
     * Vérifie si au moins une valeur est présente dans un tableau
     * @param array $listeValeurs
     * @param array $array
     * @param string $prefixeValeur
     * @return boolean
     */
    public static function isUneValeurPresente(array $listeValeurs, array $array, $prefixeValeur = '') {
        foreach ($listeValeurs as $valeur) {
            // Si au moins une clé est trouvé
            if (in_array($prefixeValeur . $valeur, $array)) {
                return TRUE;
            }
        }

        // Si aucune clé n'a été trouvée
        return FALSE;
    }

    /**
     * Vérifie si toutes les clés sont présentes dans un tableau
     * @param array $listeCle
     * @param array $array
     * @param string $prefixeCle
     * @return boolean
     */
    public static function isToutesClesPresentes(array $listeCle, array $array, $prefixeCle = '') {
        // S'il n'y a pas de clé
        if (empty($listeCle)) {
            return FALSE;
        }

        foreach ($listeCle as $cle) {
            // Si une des clés n'est pas présente
            if (!array_key_exists($prefixeCle . $cle, $array)) {
                return FALSE;
            }
        }

        // Si toutes les clés ont été trouvées
        return TRUE;
    }

    /**
     * Vérifie si au moins une valeur non vide est présente dans un tableau
     * @param array $row
     * @param array $indexColonnes
     * @param string $prefixeCle
     * @return boolean
     */
    public static function isUneValeurNonVide(array $row, array $indexColonnes = [0], $prefixeCle = '') {
        if (empty($row)) {
            return FALSE;
        }

        foreach ($indexColonnes as $index) {
            $valeur = trim(static::getVal($row, $prefixeCle . $index));
            if (!empty($valeur)) {
                return TRUE;
            }
        }

        return FALSE;
    }

}
