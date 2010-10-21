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

        $tiers = $this->getUser()->getTiers();

        if(isset($tiers) && substr($tiers->mot_de_passe,0,6) == '{SSHA}') {
            $this->redirect('@mon_espace_civa');
        }else {
            $this->form = new FirstConnectionForm();
            if ($request->isMethod(sfWebRequest::POST)) {
                $this->form->bind($request->getParameter($this->form->getName()));

                if ($this->form->isValid()) {
                    if(!$tiers) $this->getUser()->signIn($this->form->getValue('tiers'));
                    $this->redirect('compte/create');
                }
            }
        }

    }

    public function executeCreate(sfWebRequest $request) {
        $tiers = $this->getUser()->getTiers();
        if(isset($tiers) && substr($tiers->mot_de_passe,0,6) == '{SSHA}') {
            $this->redirect('@mon_espace_civa');
        }elseif($tiers) {

            $this->form = new CreateCompteForm();
            $this->email = $tiers->email;
            if($request->getParameter('act')){
                 $this->act = '?act='.$request->getParameter('act');
            }else{
                 $this->act = '';
            }

            if ($request->isMethod(sfWebRequest::POST)) {
                if($request->getParameter('act')== "modifMdp"){
                     $flash_message = "Votre mot de passe a bien été modifié.";
                     $mess_email = "votre mot de passe sur le site du CIVA vient d\'etre modifié.";
                     $obj_email = "Changement de votre mot de passe";
                }else{
                     $flash_message = "Votre compte a bien été créé.";
                     $mess_email = "votre compte a bien été créé sur le site du CIVA.";
                     $obj_email = "Création de votre compte";
                }

                $this->form->bind($request->getParameter($this->form->getName()));
                $ldap = new ldap();
                if ($this->form->isValid()) {
                    $tiers = $this->form->getValue('tiers');
                    $verify = $ldap->ldapVerifieExistence($tiers);
                    if($verify) {
		      $ldap->ldapModify($tiers);
		      $mess = 'Bonjour '.$tiers->nom.',

'.$mess_email.'

Cordialement,

Le CIVA';

                        //send email
			try {
			  $message = $this->getMailer()->compose('ne_pas_repondre@civa.fr',
								 $tiers->email,
								 'CIVA - '.$obj_email,
								 $mess
								 );
			  $this->getMailer()->send($message);
                          $this->getUser()->setFlash('mdp_modif', $flash_message);
			}catch(Exception $e) {
			  $this->getUser()->setFlash('error', "Problème de configuration : l'email n'a pu être envoyé");
			}
                    }else {
                        $ldap->ldapAdd($tiers);
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

        $tiers = $this->getUser()->getTiers();
        $this->email = $tiers->email;

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {
                /*$values['email'] = $tiers->email;
                $values['mot_de_passe'] = $tiers->mot_de_passe;*/
                $ldap = new ldap();
                $ldap->ldapModify($tiers);
                $this->getUser()->setFlash('maj', 'Vos identifiants ont bien été mis à jour.');
                $this->redirect('@mon_compte');
            }else {
                $this->form_modif_err = 1;
            }
        }

    }

    public function executeMotdepasseOublie(sfWebRequest $request) {
        $this->form = new LoginForm();
        $tiers = sfCouchdbManager::getClient('Tiers')->retrieveByCvi($request->getParameter('cvi'));

        if($request->getParameter('cvi')) {
            $mdp =$request->getParameter('mdp');

            if($tiers && $tiers->mot_de_passe==$mdp) {
                $this->getUser()->signIn($tiers);
                $this->redirect('compte/create?act=modifMdp');
            }
        }else {
            if ($request->isMethod(sfWebRequest::POST)) {
                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {
                    $tiers = $this->form->getValue('tiers');
                    $tiers->mot_de_passe = sprintf("%04d", rand(0, 9999));
                    $tiers->save();

                    $mess = 'Bonjour '.$tiers->nom.',

vous avez oublié votre mot de passe pour le redéfinir merci de cliquer sur le lien suivant :

'.sfConfig::get('app_base_url').'compte/motdepasseOublie?cvi='.$tiers->cvi.'&mdp='.$tiers->mot_de_passe.'

Cordialement,

Le CIVA';
                    //send email
		    try {
		      $message = $this->getMailer()->compose('ne_pas_repondre@civa.fr',
							     $tiers->email,
							     'CIVA - Mot de passe oublié',
							     $mess
							     );
		      $this->getMailer()->send($message);
		      $this->getUser()->setFlash('email_send', 'Un email vient de vous etre envoyé. Veuillez cliquer sur le lien contenu dans cet email afin de redéfinir votre mot de passe');
		    }catch(Exception $e) {
		      $this->getUser()->setFlash('error', "Problème de configuration : l'email n'a pu être envoyé");
		    }

                }
            }
        }

    }

}
