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

/**
 * Classe utilitaire
 *
 * @author bruno
 */
class HString {

    private static function getXMLCaracteresEntites() {
        return array(array('&', '>', '<', '"', "'"), array('&amp;', '&gt;', '&lt;', '&quot;', '&apos;'));
    }

    /**
     * Permet d'échapper les caractères spécifiques au XML
     * @param type $chaine
     * @return type
     */
    public static function encodeXML($chaine) {
        $xml_car_ent = static::getXMLCaracteresEntites();
        return str_replace($xml_car_ent[0], $xml_car_ent[1], $chaine);
    }

    /**
     * Permet de décoder une chaine XML encodée
     * @param type $chaine
     * @return type
     */
    public static function decodeXML($chaine) {
        $xml_car_ent = static::getXMLCaracteresEntites();
        return str_replace($xml_car_ent[1], $xml_car_ent[0], $chaine);
    }

    /**
     * Utile pour "trimer" les éléments d'un tableau
     * @param mixed $value : La valeur à traiter.
     */
    public static function appliquerTrim(&$value) {
        $value = trim($value);
    }

    /**
     * enlève les espaces superflus ainsi que les tabulations et les retours chariot
     * @param type $chaine
     * @return type
     */
    private static function trimXL($chaine) {
        return preg_replace('( +)', ' ', str_replace("\n", ' ', str_replace("\r", ' ', str_replace("\t", ' ', trim($chaine)))));
    }

    /**
     * On enlève les accents d'une chaine
     * @param type $chaine
     * @return type
     */
    private static function sansAccent($chaine) {
        $t_string = trim(mb_strtolower(html_entity_decode($chaine, ENT_QUOTES)));
        $accent = array('à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð'
            , 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ü', 'ù', 'ú', 'û', 'ý', 'ý', 'þ', 'ÿ');
        $noaccent = array('a', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o'
            , 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'b', 'y');

        return str_replace($accent, $noaccent, $t_string);
    }

    /**
     * Remplace les caractères spéciaux par des espaces
     * @param type $chaine
     * @return type
     */
    private static function caracteresSpeciaux($chaine) {
        $t_string = trim(mb_strtolower(html_entity_decode($chaine, ENT_QUOTES)));
        $spec = array("'", '-', '%', '&', ',', ';', ':', '.', '_', '(', ')', '@', '|', '_', '"', '#', '\\'
            , '/', '*', '+', '=', '}', '!', '{', '[', ']', '?', '°', '€');
        $nospec = array(' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '
            , ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ');

        return static::trimXL(preg_replace('`( | |Â|&nbsp;)+`i', ' ', (str_replace($spec, $nospec, $t_string))));
    }

    /**
     * Remplacer les caractères spéciaux pour le titre de l'article
     * @param type $chaine
     */
    public static function remplacerPourTitre($chaine) {
        $tbl_remplacement = [
            // Fractions
            '¼' => '1/4', '½' => '1/2', '¾' => '3/4', '?' => '1/3', '?' => '2/3',
            // Caractères interdits
            '<' => '', '>' => '', ';' => '', '#' => '', '{' => '', '}' => '', '~' => '', '`' => '', '^' => '', '¨' => '',
            // Considérés comme espace
            '|' => ' ',
            // Autres
            '´' => '\''
        ];

        foreach ($tbl_remplacement as $from => $to)
            $chaine = str_replace($from, $to, $chaine);

        return trim($chaine);
    }

    /**
     * URL rewriting
     * @param type $value
     * @return string
     */
    public static function genNomUrl($value) {
        $t_string = trim(strtolower(static::caracteresSpeciaux(static::sansAccent($value))), '-');
        return $t_string;
    }

    /**
     * CamelCase to underscore_case conversion
     * @param string $str_camel
     * @param string $separator
     * @return string
     */
    public static function uncamel($str_camel, $separator = '_') {
        if (empty($separator))
            return $str_camel;
        $str_camel[0] = strtolower($str_camel[0]);
        return strtolower(preg_replace('/([A-Z])/', $separator . '$1', $str_camel));
    }

    /**
     * underscore_case to CamelCase conversion
     * @param string $str_underscore
     * @param string $separator
     * @return string
     */
    public static function camel($str_underscore, $separator = '_') {
        if (empty($separator))
            return $str_underscore;
        $words = str_replace($separator, ' ', $str_underscore);
        return str_replace(' ', '', ucwords($words));
    }

    public static function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }

    public static function endsWith($haystack, $needle) {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }

    /**
     * Récupération du chemin d'un fichier à partir du nom de classe utilisant les namespaces
     * La classe doit être présente dans le namespace initial $prefix
     * @param string $class
     * @param string $prefix
     * @param boolean $removePrefix
     * @return string
     */
    public static function getNamespacedClassPath($class, $prefix, $removePrefix = TRUE) {
        // Remove first \ if present
        $classname = ltrim($class, '\\');
        // does the class use the prefix?
        $len = strlen($prefix);
        if (strncmp($prefix, $classname, $len) !== 0) {
            // no, return NULL
            return NULL;
        }

        $relative_class_name = $removePrefix ? substr($classname, $len) : $classname;

        // Récupération du chemin avec les namespaces
        return Conf::getConfByCode(Conf::BASE_DIR) . str_replace('\\', DIRECTORY_SEPARATOR, $relative_class_name) . '.php';
    }

    /**
     * Obtenir le nom d'une classe sans son espace de nom
     * @param string $class
     * @return string
     */
    public static function getClassnameWithoutNamespace($class) {
        $refl = new \ReflectionClass($class);
        return $refl->getShortName();
    }

    /**
     * Tester si une chaine commence par une autre chaine
     * @param type $chaine la chaine a tester
     * @param type $debut la chaine de debut
     * @return boolean
     */
    public static function commencePar($chaine, $debut) {
        return mb_substr($chaine, 0, strlen($debut)) == $debut;
    }

    /**
     * Tester si une chaine termine par une autre chaine
     * @param string $chaine la chaine a tester
     * @param string $end la chaine de fin
     * @return boolean
     */
    public static function terminePar($chaine, $end) {
        return substr($chaine, -strlen($end)) == $end;
    }

}
