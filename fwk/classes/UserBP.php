<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace fwk\classes;

// Services
use fwk\services\user\Login;
use fwk\services\user\Edit;
use fwk\services\user\Newpassword;
use fwk\services\user\Forgotpwd;
use fwk\services\user\InscriptionVue;
use fwk\services\user\Inscription;
// Helpers
use fwk\utils\HSession;
use fwk\utils\Html;

/**
 * Description of User
 *
 * @author bruno
 */
class UserBP {

    /**
     * Obtenir le formulaire de connexion
     * @return string
     */
    public static function getConnexionForm() {
        return '<form action="' . Login::getUrl() . '" class="form-horizontal" method="POST">
                    <div class="form-group">
                      <label for="user_email" class="col-sm-2 control-label required">Email</label>
                      <div class="col-sm-10">
                        <input type="email" class="form-control" id="user_email" name="user_email" placeholder="Email" required="required">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="user_password" class="col-xs-12 col-sm-2 control-label required">Mot de passe</label>
                      <div class="col-xs-8 col-sm-7 col-lg-8">
                        <input type="password" class="form-control" id="user_password" name="user_password" placeholder="Mot de passe" required="required">
                      </div>
                      <div class="col-xs-4 col-sm-3 col-lg-2">
                        <button type="button" id="user_forgot" class="btn btn-default cov-ug-add" url="' . Forgotpwd::getUrl() . '" data-toggle="tooltip" title="Définir un nouveau mot de passe">' . Html::getIcon('key') . ' J\'ai oublié</button>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                          <label>
                            <input type="checkbox"> Se souvenir de moi
                          </label>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="col-sm-12">
                        <button type="submit" class="btn btn-success pull-right" value="submit" name="submit" id="submit">Connexion</button>
                        <a role="button" class="btn btn-primary pull-right" href="' . InscriptionVue::getUrl() . '">Inscription</a>
                      </div>
                    </div>
                  </form>';
    }

    /**
     * Obtenir le formulaire de gestion d'un utilisateur
     * @param User $user
     * @return string
     */
    public static function getForm(User $user) {
        $html = '<form action="' . Edit::getUrl() . '" class="form-horizontal" method="POST">
                    <input type="hidden" name="id" value="' . $user->id . '" />';

        $html .= '<div class="panel panel-primary">
                        <div class="panel-heading">
                          <h3 class="panel-title">Informations personnelles</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                              <label for="prenom" class="col-sm-2 control-label required">Prénom</label>
                              <div class="col-sm-10">
                                <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom" value="' . $user->prenom . '" required="required">
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="nom" class="col-sm-2 control-label required">Nom</label>
                              <div class="col-sm-10">
                                <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" value="' . $user->nom . '" required="required">
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="email" class="col-sm-2 control-label required">Email</label>
                              <div class="col-sm-10">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="' . $user->email . '" required="required">
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="tel" class="col-sm-2 control-label">N° de téléphone</label>
                              <div class="col-sm-10">
                                <input type="text" class="form-control" id="tel" name="tel" placeholder="0601020304" value="' . $user->tel . '">
                              </div>
                            </div>';
        if (HSession::getUser()->admin) {
            $html .= '<div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                          <div class="checkbox">
                            <label>
                              <input type="checkbox" name="admin" id="admin" ' . ($user->admin ? 'checked' : '') . '> Administrateur
                            </label>
                          </div>
                        </div>
                    </div>';
        }
        $html .= '  </div>
                    </div>
                    <div class="panel panel-danger">
                        <div class="panel-heading">
                          <h3 class="panel-title">Modification de mot de passe</h3>
                        </div>
                        <div class="panel-body">
                          <div class="form-group">
                            <label for="old_password" class="col-sm-2 control-label">Ancien</label>
                            <div class="col-sm-10">
                              <input type="password" class="form-control" id="old_password" name="old_password">
                            </div>
                          </div>
                          <div class="form-group">
                            <label for="password" class="col-sm-2 control-label">Nouveau</label>
                            <div class="col-sm-10">
                              <input type="password" class="form-control" id="password" name="password">
                            </div>
                          </div>
                          <div class="form-group">
                            <label for="password_check" class="col-sm-2 control-label">Saisir à nouveau</label>
                            <div class="col-sm-10">
                              <input type="password" class="form-control" id="password_check" name="password_check">
                            </div>
                          </div>
                        </div>
                      </div>
                    <div class="form-group">
                      <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-success" value="submit" name="submit" id="submit">' . ($user->existe() ? 'Modifier' : 'Créer') . '</button>
                      </div>
                    </div>
                </form>';
        return $html;
    }

    /**
     * Obtenir le formulaire de contact
     * @param User $user
     * @return string
     */
    public static function getContactForm(User $user) {
        $html = '<form action="' . Contact::getUrl() . '" class="form-horizontal" method="POST">
                    <input type="hidden" name="id" value="' . $user->id . '" />';
        $html .= '<div class="panel panel-success">
                    <div class="panel-heading"><h3 class="panel-title">Votre message</h3></div>
                    <div class="panel-body">';
        $html .= '<div class="form-group">
                    <label for="user_cont_titre" class="col-sm-2 control-label required">Sujet</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="user_cont_titre" name="user_cont_titre" placeholder="Donnez un titre à votre message" required="required"/>
                    </div>
                  </div>';
        $html .= '<div class="form-group">
                    <label for="user_cont_message" class="col-sm-2 control-label required">Votre message</label>
                    <div class="col-sm-10">
                      <textarea id="user_cont_message" name="user_cont_message" class="form-control" rows="10" required="required"></textarea>
                    </div>
                  </div>';
        $html .= '<div class="form-group">
                    <div class="col-sm-offset-10 col-sm-2">
                      <button type="submit" class="btn btn-success pull-right" value="submit" name="submit" id="submit">Envoyer</button>
                    </div>
                  </div>';
        $html .= '</div></div></form>';
        return $html;
    }

    /**
     * Obtenir le formulaire de nouveau mot de passe
     * @param User $user
     * @return string
     */
    public static function getNewPasswordForm(User $user) {
        $html = '<form action="' . Newpassword::getUrl() . '" class="form-horizontal" method="POST">
                    <input type="hidden" name="id" value="' . $user->id . '" />
                    <input type="hidden" name="token" value="' . $user->getToken() . '" />';
        $html .= '<div class="panel panel-success">
                    <div class="panel-heading"><h3 class="panel-title">Votre nouveau mot de passe</h3></div>
                    <div class="panel-body">';
        $html .= '<div class="form-group">
                    <label for="user_password" class="col-sm-2 control-label required">Mot de passe</label>
                    <div class="col-sm-10">
                      <input type="password" class="form-control" id="user_password" name="user_password" required="required"/>
                    </div>
                  </div>';
        $html .= '<div class="form-group">
                    <label for="user_password2" class="col-sm-2 control-label required">Saisir à nouveau</label>
                    <div class="col-sm-10">
                      <input type="password" class="form-control" id="user_password2" name="user_password2" required="required"/>
                    </div>
                  </div>';
        $html .= '<div class="form-group">
                    <div class="col-sm-offset-10 col-sm-2">
                      <button type="submit" class="btn btn-success pull-right" value="submit" name="submit" id="submit">Envoyer</button>
                    </div>
                  </div>';
        $html .= '</div></div></form>';
        return $html;
    }

    /**
     * Obtenir le formulaire d'inscription
     * @return string
     */
    public static function getInscriptionForm() {
        return '<form action="' . Inscription::getUrl() . '" class="form-horizontal" method="POST">
                    <div class="form-group">
                      <label for="user_email" class="col-sm-2 control-label required">Email</label>
                      <div class="col-sm-10">
                        <input type="email" class="form-control" id="user_email" name="user_email" placeholder="Email" required="required">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="user_nom" class="col-sm-2 control-label required">Nom</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="user_nom" name="user_nom" placeholder="Nom" required="required">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="user_prenom" class="col-sm-2 control-label required">Prénom</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="user_prenom" name="user_prenom" placeholder="Prénom" required="required">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="user_tel" class="col-sm-2 control-label">N° de téléphone</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="user_tel" name="user_tel" placeholder="00 00 00 00 00 00">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="user_password" class="col-sm-2 control-label required">Mot de passe</label>
                      <div class="col-sm-10">
                        <input type="password" class="form-control" id="user_password" name="user_password" placeholder="Mot de passe" required="required">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="user_password2" class="col-sm-2 control-label required">Saisir de nouveau</label>
                      <div class="col-sm-10">
                        <input type="password" class="form-control" id="user_password2" name="user_password2" placeholder="Mot de passe" required="required">
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="col-xs-12">
                        <button type="submit" class="btn btn-success pull-right" value="submit" name="submit" id="submit">Envoyer</button>
                      </div>
                    </div>
                  </form>';
    }

}
