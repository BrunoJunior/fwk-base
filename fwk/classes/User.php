<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace fwk\classes;

use DateTime;
use fwk\utils\HMail;
use fwk\erreurs\FwkException;

/**
 * Description of User
 *
 * @author bruno
 */
class User extends UserDAO {

    /**
     * Le mot de passe envoyé est-il correcte
     * @param string $password
     * @return boolean
     */
    public function checkPassword($password) {
        if (empty($this->password) && empty($password)) {
            return TRUE;
        }
        return password_verify($password, $this->password);
    }

    /**
     * Définir le mot de passe
     * @param string $password
     */
    public function setPassword($password) {
        $this->token = '';
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Affichage simpel d'un utilisateur
     * Prénom Nom
     * @return string
     */
    public function toHtml() {
        return $this->prenom . ' ' . $this->nom;
    }

    /**
     * Vérifier si l'adresse email est déjà utilisée
     * @throws Exception
     */
    private function checkEmail() {
        $user = static::chargerParEmail($this->email);
        if ($user->existe() && $this->id != $user->id) {
            throw new FwkException('Cette adresse email est déjà utilisée !');
        }
    }

    /**
     * Interdiction de création si email déjà utilisé
     */
    protected function avantAjout() {
        $this->checkEmail();
    }

    /**
     * Interdiction de modification si email déjà utilisé
     */
    protected function avantModification() {
        $this->checkEmail();
    }

    /**
     * Contacter un membres
     * @param string $sujet
     * @param string $message
     * @throws Exception
     * @return boolean Description
     */
    public function contacter($sujet, $message) {
        $connectedUser = HSession::getUser();
        if (!$connectedUser->existe()) {
            $connectedUser->email = 'no-reply@co-voiturage.bdesprez.com';
            $connectedUser->prenom = 'Co-voiturage';
            $connectedUser->nom = '[Mot de passe]';
        }
        if (empty($connectedUser->email)) {
            throw new FwkException('Veuillez configurer votre adresse email !');
        }
        if ($connectedUser->id == $this->id) {
            throw new FwkException('Vous ne pouvez vous contacter vous-même !');
        }
        if (empty($this->email)) {
            throw new FwkException('Cet utilisateur n\'a pas renseigné son adresse email !');
        }
        return HMail::envoyer($connectedUser, $this->email, $sujet, $message);
    }

    /**
     * Essaie d'obtenir un token pour envoyer un oubli de mot de passe
     * @return string
     * @throws Exception
     */
    public function getNewToken() {
        if (!empty($this->lastforgot)) {
            $dtForgot = DateTime::createFromFormat('Y-m-d H:i:s', $this->lastforgot);
            $dtNow = new DateTime('now');
            $diff = $dtNow->getTimestamp() - $dtForgot->getTimestamp();
            if ($diff < (86400)) {
                throw new FwkException('Vous devez attendre 24h avant de pouvoir générer un nouveau mot de passe !');
            }
        }
        $this->token = HMail::getToken();
        $this->lastforgot = date('Y-m-d H:i:s');
        $this->merger();
        return $this->token;
    }

    /**
     * Vérification du token passé en paramètre
     * @param string $token
     * @throws Exception
     */
    public function checkToken($token) {
        if (empty($this->token) || empty($token) || $this->token != $token) {
            throw new FwkException('Erreur d\'authentification');
        }
    }

    /**
     * Obtenir le token de vérification
     * @return string
     */
    public function getToken() {
        return $this->token;
    }

}
