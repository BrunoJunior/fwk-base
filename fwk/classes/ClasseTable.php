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

namespace fwk\classes;

// Helpers
use fwk\utils\Cache;
use fwk\utils\HDatabase;
use fwk\utils\HLog;
use fwk\utils\HString;
// Table
use fwk\classes\schema\ChampTable;
use fwk\classes\schema\Table;
use fwk\erreurs\FwkException;

/**
 * Description of XLPosClass
 *
 * @author bruno
 */
abstract class ClasseTable extends ClasseSimple {

    /**
     * Etats de la donnée
     */
    const ETAT_CREE = 'C';
    const ETAT_MODIFIE = 'M';
    const ETAT_SUPPRIME = 'S';

    /**
     * Modes du chargement de liste
     */
    const MODE_NORMAL = 0;
    const MODE_COUNT = 1;
    const MODE_NBPAGES = 2;

    /**
     * @var int
     */
    public $id;

    /**
     * @var Table[]
     */
    protected static $tables = [];

    /**
     * Constructeur
     * @param int $id
     */
    public function __construct($id = null) {
        if (isset($id))
            $this->charger($id);
    }

    /**
     * Permet de définir la table d'une classe
     * A surcharger
     * @return Table
     */
    protected static function defineTable() {
        return new Table('table', []);
    }

    /**
     * Donne les informations de la BDD (colonnes, taille, clé primaire ...)
     * @return Table
     */
    public static function getTable() {
        $classname = get_called_class();
        if (!array_key_exists($classname, static::$tables)) {
            static::$tables[$classname] = static::defineTable();
        }
        return static::$tables[$classname];
    }

    /**
     * Charge un objet grâce à son identifiant technique
     * @param int $id
     */
    public function charger($id) {
        $this->chargerPar('id', $id);
    }

    /**
     * Select par défaut pour toutes les colonnes d'une table
     * @return string
     */
    protected static function getSqlSelect($count = FALSE) {
        return 'SELECT ' . ($count ? 'COUNT(`' . static::getTable()->nom . '`.*)' : '`' . static::getTable()->nom . '`.*') . ' FROM `' . static::getTable()->nom . '`';
    }

    /**
     * Gestion du cache lors de l'appel d'une méthode de chargement de donnée
     * @param string $nom_cle
     * @param mixed $valeurs_cle (valeur ou tableau de valeurs)
     */
    public function chargerPar($nom_cle, $valeurs_cle) {
        HLog::log('Debut chargerPar ' . $nom_cle . '. Params : ' . json_encode($valeurs_cle), '', true, HLog::DEBUG);

        $cle = $valeurs_cle;
        if (is_array($cle))
            $cle = implode('|', $cle);

        $params = $valeurs_cle;
        if (!is_array($params))
            $params = array($params);

        $found = Cache::get(get_class($this), $cle, $nom_cle);
        if (isset($found))
            $this->chargerFromStdClass($found);
        else {
            $requete = $this->getRequetePar($nom_cle);
            $resultat = HDatabase::rechercher($requete, $params);
            HLog::log('Requete chargerPar ' . $requete, '', true, HLog::DEBUG);

            if (!empty($resultat)) {
                $this->chargerFromLigne($resultat[0]);
                Cache::add(get_class($this), $cle, $this, $nom_cle);
                if (empty($nom_cle) || $nom_cle != 'id')
                    Cache::add(get_class($this), $this->id, $this, 'id');
            }
        }

        HLog::log('Fin chargerPar ' . $nom_cle . '. Resultat : ' . json_encode($this), '', true, HLog::DEBUG);
    }

    /**
     * Compter le nombre d'éléments d'une requête de liste
     * @param string $requete
     * @param array $params
     */
    public static function getCountListe($requete = '', $params = []) {
        // FIXME - COMPTER SOUS REQUETE
        if (empty($requete)) {
            $requete = static::getSqlSelect();
        }
        //$sqlCount = 'SELECT COUNT(*) nb FROM (' . $requete . ')';
        $countResult = HDatabase::rechercher($requete, $params);
        return count($countResult);
    }

    /**
     * Obtenir le nombre de pages pour une requête et un nombre max par page
     * @param string $requete
     * @param array $params
     * @param int $nbMax
     * @return int
     */
    public static function getNbPages($requete = '', $params = [], $nbMax = 0) {
        if ($nbMax < 1) {
            return 1;
        }
        $nb = static::getCountListe($requete, $params);
        $nbPages = intval($nb / $nbMax);
        $reste = $nb % $nbMax;
        if ($reste > 0) {
            $nbPages++;
        }
        return $nbPages;
    }

    /**
     * Récupérer une liste d'objet à partir d'une requête et de ses paramètres associés
     * @param string $requete
     * @param array $params
     * @param int $nbMax Nombre d'éléments max affichés
     * @param int $page Page à afficher (ne fonctionne qu'en présence de $nbMax)
     * @param int $mode Mode de chargement
     * @return int|ClasseTable
     */
    public static function getListe($requete = '', $params = [], $nbMax = 0, $page = 1, $mode = self::MODE_NORMAL) {
        $nomClasse = get_called_class();

        if (empty($requete)) {
            $requete = static::getSqlSelect();
        }

        if ($mode === static::MODE_COUNT) {
            return static::getCountListe($requete, $params);
        }

        if ($mode === static::MODE_NBPAGES) {
            return static::getNbPages($requete, $params, $nbMax);
        }

        $offset = 0;
        if ($nbMax > 0) {
            $offset = $nbMax * ($page - 1);
            $requete .= ' LIMIT ' . $nbMax . ' OFFSET ' . $offset;
        }

        HLog::log('Debut getListe ' . $requete . '. Params : ' . json_encode($params), '', true, HLog::DEBUG);

        $resultat = HDatabase::rechercher($requete, $params);
        $liste = [];

        if (!empty($resultat)) {
            foreach ($resultat as $row) {
                $classe = new $nomClasse();
                $classe->chargerFromLigne($row);
                $liste[] = $classe;
                Cache::add(get_class($classe), $classe->id, $classe, 'id');
            }
        }

        HLog::log('Fin getListe ' . $requete . '. Result : ' . json_encode($liste), '', true, HLog::DEBUG);
        return $liste;
    }

    /**
     * A redéfinir au besoin.
     * Retourne la requête à exécuter suivant le nom de la clé
     * @param string $nom_cle
     * @return string
     */
    protected function getRequetePar($nom_cle) {
        if (empty($nom_cle) || $nom_cle = 'id')
            return 'SELECT * FROM `' . static::getTable()->nom . '` WHERE ' . static::getTable()->champ_primaire->nom_colonne . ' = ?';
        else
            return '';
    }

    /**
     * Vérification de la donnée avant merge
     */
    protected function verifierAvantMerge() {
        foreach (static::getTable()->champs as $champ) {
            if ($champ->persiste) {
                $attribut = $champ->nom_attribut;
                if ($champ->obligatoire && (!isset($this->$attribut) || $this->$attribut === '')) {
                    throw new FwkException('L\'attribut ' . $attribut . ' ne peut être vide !');
                }
                if (isset($champ->taille) && mb_strlen(strval($this->$attribut)) > $champ->taille) {
                    throw new FwkException('L\'attribut ' . $attribut . ' ne peut dépasser ' . $champ->taille . ' charactères !');
                }
            }
        }
    }

    /**
     * Création de la donnée en BDD
     * @return boolean
     */
    protected function ajouter($obj_source = null) {
        // Les champs qui ne sont pas obligatoires mais qui ne doivent pas être vides, sont obligatoires en création
        if (isset($obj_source))
            $this->verifierParametres($obj_source, true);

        $this->verifierAvantMerge();
        $this->avantAjout();

        $colonnes = array();
        $params = array();
        $values = array();

        $this->remplirColonnesEtValeursAjout($colonnes, $values, $params);

        $requete = 'INSERT INTO `' . static::getTable()->nom . '` (' . implode(',', $colonnes) . ') VALUES (' . implode(',', $values) . ')';
        $is_ok = HDatabase::executer($requete, $params);

        if ($is_ok)
            $this->id = HDatabase::getDernierIdCree();
        else
            throw new FwkException('Erreur lors de l\'insertion de la donnée');
        $this->apresAjout();
    }

    /**
     *
     * @param string[] $sets
     * @param string[] $params
     */
    private function remplirColonnesEtValeursAjout(&$colonnes, &$values, &$params) {
        foreach (static::getTable()->champs as $champ) {
            if ($champ->persiste) {
                $attribut = $champ->nom_attribut;
                $attr_valeur = $this->$attribut;
                if (isset($attr_valeur) && !is_null($attr_valeur)) {
                    $colonnes[] = '`' . $champ->nom_colonne . '`';
                    $params[$champ->nom_colonne] = $this->transformerValeurPourBdd($attribut);
                    $values[] = ':' . $champ->nom_colonne;
                }
            }
        }
    }

    /**
     * Par défaut la valeur sauvegardée en BDD est celle sur l'objet
     * @param string $attribut
     * @return mixed
     */
    protected function transformerValeurPourBdd($attribut) {
        $champ = static::getTable()->getChamp($attribut);
        $valeur = $this->$attribut;
        if ($champ !== NULL) {
            switch ($champ->type) {
                case 'date':
                    $valeur = HDatabase::convertDateForBDD($valeur);
                    break;
            }
        }
        return $valeur;
    }

    /**
     * Par défaut la valeur chargée est celle de la BDD
     * @param string $attribut
     * @param mixed $value
     * @return mixed
     */
    protected function transformerValeurFromBdd($attribut, $value) {
        $champ = static::getTable()->getChamp($attribut);
        $valeur = $value;
        if ($champ !== NULL) {
            switch ($champ->type) {
                case 'date':
                    $valeur = HDatabase::convertDateFromBDD($valeur);
                    break;
            }
        }
        return $valeur;
    }

    /**
     *
     * @param string[] $sets
     * @param string[] $params
     */
    private function remplirColonnesEtValeursUpdate(&$sets, &$params) {
        foreach (static::getTable()->champs as $champ) {
            if ($champ->persiste) {
                $attribut = $champ->nom_attribut;
                $attr_valeur = $this->$attribut;
                if (isset($attr_valeur) && !is_null($attr_valeur)) {
                    $sets[] = '`' . $champ->nom_colonne . '` = ?';
                    $params[] = $this->transformerValeurPourBdd($attribut);
                }
            }
        }
        $params[] = $this->id;
    }

    /**
     * Modification de la donnée en BDD
     */
    protected function modifier() {
        $this->verifierAvantMerge();
        $this->avantModification();
        $sets = array();
        $params = array();

        $this->remplirColonnesEtValeursUpdate($sets, $params);
        $requete = 'UPDATE `' . static::getTable()->nom . '` SET ' . implode(',', $sets) . ' WHERE ' . static::getTable()->champ_primaire->nom_colonne . ' = ?';

        if (!HDatabase::executer($requete, $params))
            throw new FwkException('Erreur lors de la mise à jour de la donnée');
        $this->apresModification();
    }

    /**
     * Vérifie qu'un objet a été chargé (id non null)
     * @return boolean
     */
    public function existe() {
        return !empty($this->id);
    }

    /**
     * Création ou mise à jour de la donnée en BDD
     * @param \stdClass $obj_source optional - La \stdClass source avec laquelle on a rempli l'objet
     */
    public function merger($obj_source = null) {
        if ($this->existe()) {
            $this->modifier();
        } else {
            $this->ajouter($obj_source);
        }
        if (!Cache::contains(get_class(), 'success') && ((Cache::get('', 'service')) instanceof ServiceVue)) {
            echo '<div class="alert alert-success" role="alert">Enregistrement effectué</div>';
            Cache::add(get_class(), 'success', 1);
        }
    }

    /**
     * Suppression de la donnée en BDD
     * @return boolean
     */
    public function supprimer() {
        if (!$this->existe())
            return false;

        $this->avantSuppression();

        $requete = 'DELETE FROM `' . static::getTable()->nom . '` WHERE ' . static::getTable()->champ_primaire->nom_colonne . ' = ?';
        $this->reponse->etat = static::ETAT_SUPPRIME;
        $is_ok = HDatabase::executer($requete, [$this->id]);

        $this->apresSuppression();

        return $is_ok;
    }

    /**
     * Rempli l'objet à partir d'une ligne de résultat de requête
     * @param array $ligne
     */
    public function chargerFromLigne($ligne) {
        HLog::log('Debut chargerFromLigne ' . json_encode($ligne), '', true, HLog::DEBUG);

        foreach ($ligne as $col => $value) {
            // Colonne non présente dans les champs ... on passe au suivant
            if (!array_key_exists($col, static::getTable()->champs_nom_colonne))
                continue;
            $attribut = static::getTable()->champs_nom_colonne[$col]->nom_attribut;
            if (property_exists($this, $attribut))
                $this->$attribut = $this->transformerValeurFromBdd($attribut, $value);
        }

        HLog::log('Fin chargerFromLigne : ' . json_encode($this), '', true, HLog::DEBUG);
    }

    /**
     * Création de la table en BDD
     * @return type
     */
    protected function creerTable() {
        $query = 'CREATE TABLE `' . static::getTable()->nom . '` (' . static::getTable()->champ_primaire->nom_colonne . ' INT NOT NULL AUTO_INCREMENT PRIMARY KEY,';
        $fks = [];
        foreach (static::getTable()->champs as $champ) {
            if ($champ->persiste) {
                $query .= '`' . $champ->nom_colonne . '` ' . $champ->type;
                if (isset($champ->taille))
                    $query .= '(' . $champ->taille . ')';
                if ($champ->non_vide)
                    $query .= ' NOT NULL';
                $query .= ',';
            }
            if ($champ->tableFk !== NULL) {
                $fks[] = $champ;
            }
        }
        // Création des FK
        foreach ($fks as $champFk) {
            $reference = $champFk->tableFk;
            $query .= 'CONSTRAINT `' . $champFk->getNomFk() . '` FOREIGN KEY (`' . $champFk->nom_colonne . '`) REFERENCES `' . $reference->nom . '`(`' . $reference->champ_primaire->nom_colonne . '`),';
        }

        $query = rtrim($query, ',') . ') ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci';

        if (!HDatabase::executer($query)) {
            throw new FwkException('Création table ' . static::getTable()->nom . ' KO');
        }
        return TRUE;
    }

    /**
     * Mise à jour de la table (ajout de colonnes)
     */
    protected function majTable() {
        $requete_verif_exist_col = 'SHOW COLUMNS FROM `' . static::getTable()->nom . '` LIKE ?';
        $fks = [];
        foreach (static::getTable()->champs as $champ) {
            if ($champ->persiste) {
                $tbl_resultat_col = HDatabase::rechercher($requete_verif_exist_col, [$champ->nom_colonne]);
                $type = empty($tbl_resultat_col) ? 'ADD' : 'MODIFY';
                $requete_ajout_col = 'ALTER TABLE `' . static::getTable()->nom . '` ' . $type . ' `' . $champ->nom_colonne . '` ' . $champ->type;
                if (isset($champ->taille)) {
                    $requete_ajout_col .= '(' . $champ->taille . ')';
                }
                if ($champ->non_vide) {
                    $requete_ajout_col .= ' NOT NULL';
                }
                if (!HDatabase::executer($requete_ajout_col)) {
                    throw new FwkException('MAJ table ' . static::getTable()->nom . ' KO');
                }
            }
            if ($champ->tableFk !== NULL) {
                $fks[] = $champ;
            }
        }

        // Update des FK
        foreach ($fks as $champFk) {
            $sqlFindFk = "SELECT NULL FROM information_schema.TABLE_CONSTRAINTS WHERE
                   CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = 'FOREIGN KEY'";
            $resultFindFk = HDatabase::rechercher($sqlFindFk, [$champFk->getNomFk()]);
            if (!empty($resultFindFk)) {
                // Suppression FK
                if (!HDatabase::executer('ALTER TABLE `' . static::getTable()->nom . '` DROP FOREIGN KEY `' . $champFk->getNomFk() . '`')) {
                    throw new FwkException('DROP FK ' . $champFk->getNomFk() . ' - table ' . static::getTable()->nom . ' KO');
                }
            }
            // Création FK
            if (!HDatabase::executer('ALTER TABLE `' . static::getTable()->nom .
                    '` ADD CONSTRAINT `' . $champFk->getNomFk() .
                    '` FOREIGN KEY (`' . $champFk->nom_colonne . '`) ' .
                    'REFERENCES `'.$champFk->tableFk->nom.'`(`'.$champFk->tableFk->champ_primaire->nom_colonne.'`)')) {
                throw new FwkException('ADD FK ' . $champFk->getNomFk() . ' - table ' . static::getTable()->nom . ' KO');
            }
        }
        return TRUE;
    }

    /**
     * Vérifier que la table existe déjà ou non
     * @return boolean
     */
    protected function tableExiste() {
        $requete_verif_exist = 'SHOW TABLES LIKE ?';
        $tbl_resultat = HDatabase::rechercher($requete_verif_exist, [static::getTable()->nom]);
        return (!empty($tbl_resultat));
    }

    /**
     * Add table in database
     * @return boolean true if install is OK
     */
    public static function install() {
        $is_ok = true;
        $nom_classe = get_called_class();
        $classe = new $nom_classe();

        if ($classe->tableExiste())
            $is_ok = $classe->majTable();
        else
            $is_ok = $classe->creerTable();

        if (!$is_ok)
            HLog::log('Install table ' . $classe->table->nom . ' KO', '', true, HLog::ERROR);

        return $is_ok;
    }

    /**
     * Suppression de la table de la BDD
     * @return boolean
     */
    protected function supprimerTable() {
        return HDatabase::executer('DROP TABLE IF EXISTS `' . static::getTable()->nom . '`');
    }

    /**
     * Remove table database
     * @return boolean true if uninstall is OK
     */
    public static function uninstall() {
        $is_ok = true;
        $nom_classe = get_called_class();
        $classe = new $nom_classe();

        if ($classe->tableExiste())
            $is_ok = $classe->supprimerTable();

        if (!$is_ok)
            HLog::log('Uninstall table ' . $classe->table->nom . ' KO', '', true, HLog::ERROR);

        return $is_ok;
    }

    /**
     * Permet de vérifier les données de l'objet à importer
     * @param type $obj_verif
     */
    protected function verifierParametres($obj_verif, $est_creation = false) {
        foreach (static::getTable()->champs as $champ) {
            if ($champ->dans_parametres) {
                $nom_attribut = $champ->nom_attribut;
                if ($champ->obligatoire && (!property_exists($obj_verif, $nom_attribut) || ChampTable::estVide($obj_verif->$nom_attribut))) {
                    throw new FwkException('L\'information "' . $nom_attribut . '" est obligatoire pour traiter la donnée !');
                } elseif ($champ->non_vide && property_exists($obj_verif, $nom_attribut) && ChampTable::estVide($obj_verif->$nom_attribut)) {
                    throw new FwkException('L\'information "' . $nom_attribut . '" ne peut être vide !');
                } elseif ($champ->non_vide && $est_creation && !property_exists($obj_verif, $nom_attribut)) {
                    throw new FwkException('L\'information "' . $nom_attribut . '" est obligatoire pour créer la donnée !');
                }
            }
        }
    }

    /**
     * Avant ajout
     */
    protected function avantAjout() {

    }

    /**
     * Après ajout
     */
    protected function apresAjout() {

    }

    /**
     * Avant modification
     */
    protected function avantModification() {

    }

    /**
     * Après modification
     */
    protected function apresModification() {

    }

    /**
     * Avant suppression
     */
    protected function avantSuppression() {

    }

    /**
     * Après suppression
     */
    protected function apresSuppression() {

    }

}
