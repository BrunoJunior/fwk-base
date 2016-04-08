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

namespace fwk\classes\schema;

/**
 * Description of ChampTable
 *
 * @author bruno
 */
class ChampTable {

    /**
     * @var string
     */
    public $nom_attribut;

    /**
     * @var string
     */
    public $nom_colonne;

    /**
     * @var string
     */
    public $type;

    /**
     * @var int
     */
    public $taille;

    /**
     * @var boolean
     */
    public $primaire;

    /**
     * @var boolean
     */
    public $obligatoire;

    /**
     * @var boolean
     */
    public $non_vide;

    /**
     * @var boolean
     */
    public $persiste;

    /**
     * @var boolean
     */
    public $dans_parametres;

    /**
     * @var Table
     */
    public $table;

    /**
     * @var Table
     */
    public $tableFk;

    /**
     * Constructeur
     * @param string $nom
     * @param string $type
     * @param boolean $obligatoire
     * @param boolean $non_vide
     * @param int $taille
     * @param boolean $dans_parametres
     * @param boolean $persiste
     * @param string $nom_colonne
     * @param boolean $primaire
     * @param Table $tableFk
     */
    public function __construct($nom, $type, $obligatoire = false, $non_vide = false, $taille = null, $dans_parametres = true, $persiste = true, $nom_colonne = null, $primaire = false, $tableFk = NULL) {
        $this->nom_attribut = $nom;
        $this->type = $type;
        $this->obligatoire = $obligatoire;
        $this->non_vide = $non_vide;
        $this->taille = $taille;
        $this->primaire = $primaire;
        $this->persiste = $persiste;
        $this->dans_parametres = $dans_parametres;
        if ($persiste && isset($nom_colonne)) {
            $this->nom_colonne = $nom_colonne;
        } elseif ($persiste) {
            $this->nom_colonne = $nom;
        }
        if ($tableFk instanceof Table) {
            $this->tableFk = $tableFk;
        }
    }

    /**
     * Obtenir un champ primaire
     * @param string $nom
     * @return ChampTable
     */
    public static function getPrimaire($nom) {
        return new ChampTable('id', 'int', true, true, 11, false, true, $nom, true);
    }

    /**
     * Obtenir un champ persisté
     * @param string $nom
     * @param string $type
     * @param boolean $obligatoire
     * @param boolean $non_vide
     * @param int $taille
     * @param boolean $dans_parametres
     * @param string $nom_colonne
     * @return ChampTable
     */
    public static function getPersiste($nom, $type, $obligatoire = false, $non_vide = false, $taille = null, $dans_parametres = true, $nom_colonne = null, $tableFk = null) {
        return new ChampTable($nom, $type, $obligatoire, $non_vide, $taille, $dans_parametres, true, $nom_colonne, false, $tableFk);
    }

    /**
     * Obtenir un champ persisté de type FK
     * @param string $nom
     * @param Table $tableFk
     * @param boolean $obligatoire
     * @param boolean $non_vide
     * @param boolean $dans_parametres
     * @param string $nom_colonne
     * @return ChampTable
     */
    public static function getFk($nom, Table $tableFk, $obligatoire = false, $non_vide = false, $dans_parametres = true, $nom_colonne = null) {
        return static::getPersiste($nom, 'int', $obligatoire, $non_vide, 11, $dans_parametres, $nom_colonne, $tableFk);
    }

    /**
     * Obtenir un champ non persisté
     * @param string $nom
     * @param boolean $obligatoire
     * @param boolean $non_vide
     * @return ChampTable
     */
    public static function getNonPersiste($nom, $obligatoire = false, $non_vide = false) {
        return new ChampTable($nom, 'varchar', $obligatoire, $non_vide, null, true, false, null, false);
    }

    /**
     * Une valeur est considérée vide si :
     * 	- null ou
     * 	- '' ou
     * 	- array()
     * @param mixed $valeur
     * @return boolean
     */
    public static function estVide($valeur) {
        if (is_object($valeur))
            $valeur = (array) $valeur;
        return is_null($valeur) || $valeur === '' || (is_array($valeur) && empty($valeur));
    }

    /**
     * Le nom de la fk s'il y en a une
     * @return string
     */
    public function getNomFk() {
        if ($this->tableFk == NULL) {
            return '';
        }
        return 'fk_' . $this->table->nom . '_' . $this->nom_colonne;
    }

}
