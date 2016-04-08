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

use fwk\utils\HArray;

/**
 * Description of Table
 *
 * @author bruno
 */
class Table {

    /**
     * @var string
     */
    public $nom;

    /**
     * @var ChampTable
     */
    public $champ_primaire;

    /**
     * @var ChampTable[]
     */
    public $champs;

    /**
     * Les champs dans un tableau associatif avec le nom de la colonne en clé
     * @var ChampTable[]
     */
    public $champs_nom_colonne;

    public function __construct($nom, $champs) {
        $this->nom = $nom;
        foreach ($champs as $champ)
            $this->ajouterChamp($champ);
    }

    /**
     *
     * @param ChampTable $champ
     */
    private function ajouterChamp($champ) {
        if ($champ->primaire)
            $this->champ_primaire = $champ;
        else
            $this->champs[] = $champ;
        $this->champs_nom_colonne[$champ->nom_colonne] = $champ;
        $champ->table = $this;
    }

    /**
     * Obtenir le champ par le nom de la colonne
     * @param string $nomColonne
     * @return ChampTable
     */
    public function getChamp($nomColonne) {
        return HArray::getVal($this->champs_nom_colonne, $nomColonne, NULL);
    }

}
