<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace fwk\classes;

// Table
use fwk\classes\schema\ChampTable;
use fwk\classes\schema\Table;
use fwk\classes\ClasseTable;

// Helpers
use fwk\utils\HSession;

/**
 * Description of UserDAO
 *
 * @author bruno
 */
class UserDAO extends ClasseTable {
    /**
     * @var string
     */
    public $nom;
    /**
     * @var string
     */
    public $prenom;
    /**
     * @var string
     */
    public $tel;
    /**
     * @var string
     */
    public $email;
    /**
     * @var string
     */
    protected $password;
    /**
     * @var boolean
     */
    public $admin;
    /**
     * @var string
     */
    protected $token;
    /**
     * @var string
     */
    protected $lastforgot;

    /**
     * Table user
     */
    public static function defineTable() {
        $champs[] = ChampTable::getPrimaire('id');
        $champs[] = ChampTable::getPersiste('nom', 'varchar', true, true, 128);
        $champs[] = ChampTable::getPersiste('prenom', 'varchar', true, true, 128);
        $champs[] = ChampTable::getPersiste('tel', 'varchar', false, false, 16);
        $champs[] = ChampTable::getPersiste('email', 'varchar', false, false, 128);
        $champs[] = ChampTable::getPersiste('password', 'varchar', true, true, 128);
        $champs[] = ChampTable::getPersiste('admin', 'tinyint', false, false, 1);
        $champs[] = ChampTable::getPersiste('token', 'varchar', false, false, 128);
        $champs[] = ChampTable::getPersiste('lastforgot', 'datetime');
        return new Table('user', $champs);
    }

    /**
     * Chargement d'un utilisateur par son nom et son prénom
     * @param string $nom
     * @param string $prenom
     * @return User
     */
    public static function chargerParNomEtPrenom($nom, $prenom) {
        $sql = static::getSqlSelect();
        $sql .= ' WHERE nom = :nom AND prenom = :prenom';
        $liste = static::getListe($sql, [':nom' => $nom, ':prenom' => $prenom]);
        if (empty($liste)) {
            $user = new User();
            $user->nom = $nom;
            $user->prenom = $prenom;
            return $user;
        } else {
            return $liste[0];
        }
    }

    /**
     * Recherche un utilisateur par son email
     * @param string $email
     * @return User
     */
    public static function chargerParEmail($email) {
        $sql = static::getSqlSelect();
        $sql .= ' WHERE email = :email';
        $liste = static::getListe($sql, [':email' => $email]);
        if (empty($liste)) {
            return new User();
        } else {
            return $liste[0];
        }
    }

    /**
     * Connexion utilisateur
     * @param string $email
     * @param string $password
     * @return boolean
     */
    public static function connecter($email, $password) {
        $sql = static::getSqlSelect();
        $sql .= ' WHERE email = :email';
        $liste = static::getListe($sql, [':email' => $email]);
        if (!empty($liste)) {
            foreach ($liste as $user) {
                if ($user->checkPassword($password)) {
                    HSession::setUser($user);
                    return TRUE;
                }
            }
        }
        return FALSE;
    }

    /**
     * Transformation des données provenant de la BDD
     * @param string $attribut
     * @param mixed $value
     * @return mixed
     */
    protected function transformerValeurFromBdd($attribut, $value) {
        if ($attribut == 'admin') {
            return ($value == 1);
        }
        return parent::transformerValeurFromBdd($attribut, $value);
    }

    /**
     * Transformation des données avant envoi vers la BDD
     * @param string $attribut
     * @return mixed
     */
    protected function transformerValeurPourBdd($attribut) {
        switch ($attribut) {
            case 'admin':
                return $this->$attribut ? 1 : 0;
            default:
                return parent::transformerValeurPourBdd($attribut);
        }
    }
}
