<?php

/**
 * compte actions.
 *
 * @package    civa
 * @subpackage compte
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class compteActions extends sfActions {

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeIndex(sfWebRequest $request) {

        $recoltant = $this->getUser()->getRecoltant();

        if(isset($recoltant) && substr($recoltant->mot_de_passe,0,6) == '{SSHA}') {
            $this->redirect('@mon_espace_civa');
        }else {
            $this->form = new FirstConnectionForm();
            if ($request->isMethod(sfWebRequest::POST)) {
                $this->form->bind($request->getParameter($this->form->getName()));

                if ($this->form->isValid()) {
                    if(!$recoltant) $this->getUser()->signIn($this->form->getValue('recoltant'));
                    $this->redirect('compte/create');
                }
            }
        }

    }

    public function executeCreate(sfWebRequest $request) {
        $recoltant = $this->getUser()->getRecoltant();
        if(isset($recoltant) && substr($recoltant->mot_de_passe,0,6) == '{SSHA}') {
            $this->redirect('@mon_espace_civa');
        }elseif($recoltant) {

            $this->form = new CreateCompteForm();
            $this->email = $recoltant->email;

            if ($request->isMethod(sfWebRequest::POST)) {
                $this->form->bind($request->getParameter($this->form->getName()));
                $ldap = new ldap();
                if ($this->form->isValid()) {
                    $verify = $ldap->ldapVerifieExistence($recoltant);
                    if($verify) {
                        $newData['mot_de_passe'] = $recoltant->mot_de_passe;
                        $ldap->ldapModify($recoltant, $newData);
                        $this->getUser()->setFlash('mdp_modif', 'Votre mot de passe a bien été modifier.');
                        $mess = 'Bonjour '.$recoltant->nom.',

votre mot de passe sur le site du CIVA vient d\'etre modifié.

Cordialement,

Le CIVA';

                        //send email
                        $message = $this->getMailer()->compose('forget@civa.fr',
                                $recoltant->email,
                                'CIVA - Changement de votre mot de passe',
                                $mess
                        );
                        $this->getMailer()->send($message);
                    }else {
                        $ldap->ldapAdd($recoltant);
                    }
                    $this->redirect('@mon_espace_civa');
                }
            }
        }else {
            $this->redirect('compte');
        }
    }

    public function executeModification(sfWebRequest $request) {

        $this->form = new CreateCompteForm(null, array('verif_mdp'=>false));
        $this->form_modif_err = 0;

        $recoltant = $this->getUser()->getRecoltant();
        $this->email = $recoltant->email;

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {
                $values['email'] = $recoltant->email;
                $values['mot_de_passe'] = $recoltant->mot_de_passe;
                $ldap = new ldap();
                $ldap->ldapModify($recoltant, $values);
                $this->getUser()->setFlash('maj', 'Vos identifiants ont bien été mis à jour.');
                $this->redirect('@mon_compte');
            }else {
                $this->form_modif_err = 1;
            }
        }

    }

    public function executeMotdepasseOublie(sfWebRequest $request) {
        $this->form = new LoginForm();

        if($request->getParameter('cvi')) {
            $recoltant = sfCouchdbManager::getClient('Recoltant')->retrieveByCvi($request->getParameter('cvi'));
            $mdp =$request->getParameter('mdp');

            if($recoltant && $recoltant->mot_de_passe==$mdp) {
                $this->getUser()->signIn($recoltant);
                $this->redirect('compte/create');
            }
        }else {
            if ($request->isMethod(sfWebRequest::POST)) {
                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {
                    echo $recoltant->email;
                    $recoltant = $this->form->getValue('recoltant');
                    $recoltant->mot_de_passe = sprintf("%04d", rand(0, 9999));
                    $recoltant->save();

                    $mess = 'Bonjour '.$recoltant->nom.',

vous avez oublié votre mot de passe pour le redéfinir merci de cliquer sur le lien suivant :

http://cdevichet.civa.intra.actualys.fr/compte/motdepasseOublie?cvi='.$recoltant->cvi.'&mdp='.$recoltant->mot_de_passe.'

Cordialement,

Le CIVA';

                    //send email
                    $message = $this->getMailer()->compose('forget@civa.fr',
                            $recoltant->email,
                            'CIVA - Mot de passe oublié',
                            $mess
                    );
                    $this->getMailer()->send($message);
                    $this->getUser()->setFlash('email_send', 'Un email vient de vous etre envoyé. Veuillez cliquer sur le lien contenu dans cet email afin de redéfinir votre mot de passe');

                }
            }
        }

    }

    public function executeResetMDP(sfWebRequest $request) {
        $recoltant = sfCouchdbManager::getClient('Recoltant')->retrieveByCvi('6823700100');
        $recoltant->mot_de_passe = '{TEXT}0000';
        $recoltant->save();
        $ldap = new ldap();
        $ldapDelete = $ldap->ldapDelete($recoltant);
        echo "recoltant remis a 0 - effacer du LDAP";
        exit();
    }



}
