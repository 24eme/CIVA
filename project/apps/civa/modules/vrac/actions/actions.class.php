<?php
class vracActions extends sfActions
{
	public function executeSelectionType(sfWebRequest $request)
    {
    	$this->forward404Unless($this->type = $request->getParameter('type'));
    	$this->getUser()->setAttribute('vrac_type_tiers', $this->type);
		if($request->getParameter('createur')) {
    		$this->getUser()->setAttribute('vrac_createur', $request->getParameter('createur'));
		}

    	return $this->redirect('vrac_nouveau', array('papier' => $request->getParameter('papier', 0)));
    }

    public function executeNouveau(sfWebRequest $request)
    {
        $this->secureVrac(VracSecurity::CREATION, null);
        $this->getUser()->setAttribute('vrac_object', null);
        $this->getUser()->setAttribute('vrac_acteur', null);
        $this->getUser()->setAttribute('vrac_papier', (bool) $request->getParameter('papier', false));

    	$etapes = VracEtapes::getInstance();
    	return $this->redirect('vrac_etape', array('sf_subject' => new Vrac(), 'etape' => $etapes->getFirst()));
    }

	public function executeHistorique(sfWebRequest $request)
	{
		$this->compte = $this->getRoute()->getCompte();
        $this->secureVrac(VracSecurity::DECLARANT, null);
		$this->cleanSessions();

		$this->campagne = $request->getParameter('campagne');
		$this->statut = $request->getParameter('statut');
        $this->type = $request->getParameter('type');
        $this->temporalite = $request->getParameter('temporalite');
		$this->role = $request->getParameter('role');
        $this->commercial = $request->getParameter('commercial');

		if (!$this->campagne) {
			$this->campagne = VracClient::getInstance()->buildCampagneVrac(date('Y-m-d'));
		}
		$etablissements = $this->compte->getSociete()->getEtablissementsObject(false, true);
        $this->vracs = VracTousView::getInstance()->findSortedByDeclarants($etablissements, $this->campagne, $this->statut, $this->type, $this->role, $this->commercial, $this->temporalite);
        $this->campagnes = $this->getCampagnes(VracTousView::getInstance()->findSortedByDeclarants($etablissements), VracClient::getInstance()->buildCampagneVrac(date('Y-m-d')));
        $this->statuts = $this->getStatuts();
        $this->types = VracClient::getContratTypes();
        $this->temporalites = VracClient::$_contrat_temporalites;
        $this->roles = $this->findRoles();
        $annuaire = $this->getAnnuaire();
        $this->commerciaux = (count($annuaire->commerciaux) > 0)? $annuaire->getAnnuaireSorted('commerciaux') : array();
	}

    protected function findRoles() {
        $vracs = VracTousView::getInstance()->findSortedByDeclarants($this->getUser()->getDeclarantsVrac());
        $roles = VracClient::getRoles();
        $roles_vrac = array();
        foreach($vracs as $vrac) {
            if(array_key_exists($vrac->value->role, $roles_vrac)) {
                continue;
            }
            $roles_vrac[$vrac->value->role] = $roles[$vrac->value->role];
        }

        return $roles_vrac;
    }

    public function executeExportCSV(sfWebRequest $request)
    {
		$this->compte = $this->getRoute()->getCompte();
        $this->secureVrac(VracSecurity::DECLARANT, null);
        $this->vracs = VracTousView::getInstance()->findSortedByDeclarants(VracClient::getInstance()->getEtablissements($this->compte->getSociete()));
        $this->setLayout(false);
        $this->setResponseCsv(sprintf('export_contrats_%s.csv', date('Ymd')));
    }

    protected function setResponseCsv($filename) {
        $this->response->setContentType('application/csv');
        $this->response->setHttpHeader('Content-disposition', 'filename='.$filename, true);
        $this->response->setHttpHeader('Pragma', 'o-cache', true);
        $this->response->setHttpHeader('Expires', '0', true);
    }


	public function executeAnnuaire(sfWebRequest $request)
	{
		$this->type = $request->getParameter('type');
		$this->acteur = $request->getParameter('acteur');
		$types = array_keys(AnnuaireClient::getAnnuaireTypes());
		$acteurs = Vrac::getTypesTiers();
		if (!in_array($this->type, $types)) {
			throw new sfError404Exception('Le type "'.$this->type.'" n\'est pas pris en charge.');
		}
		if (!in_array($this->acteur, $acteurs)) {
			throw new sfError404Exception('L\'acteur "'.$this->acteur.'" n\'est pas pris en charge.');
		}

		$this->vrac = $this->populateVracTiers($this->getRoute()->getVrac());

        $this->secureVrac(VracSecurity::EDITION, $this->vrac);

		$this->annuaire = $this->getAnnuaire();
		$this->form = new VracSoussignesAnnuaireForm($this->vrac, $this->annuaire);
		if ($request->isMethod(sfWebRequest::POST)) {
			$parameters = $request->getParameter($this->form->getName());
			unset($parameters['_csrf_token']);
    		$this->form->bind($parameters);
        	if ($this->form->isValid()) {
        		$this->vrac = $this->form->getUpdatedVrac();
        	} else {
        		throw new sfException($this->form->renderGlobalErrors());
        	}
		}
		$this->getUser()->setAttribute('vrac_object', serialize($this->vrac));
		$this->getUser()->setAttribute('vrac_acteur', $this->acteur);
		return $this->redirect('annuaire_selectionner', array('type' => $this->type));
	}

	public function executeAnnuaireCommercial(sfWebRequest $request)
	{
		$this->vrac = $this->populateVracTiers($this->getRoute()->getVrac());

        $this->secureVrac(VracSecurity::EDITION, $this->vrac);

		$this->annuaire = $this->getAnnuaire();
		$this->form = new VracSoussignesAnnuaireForm($this->vrac, $this->annuaire);
		if ($request->isMethod(sfWebRequest::POST)) {
			$parameters = $request->getParameter($this->form->getName());
			unset($parameters['_csrf_token']);
    		$this->form->bind($parameters);
        	if ($this->form->isValid()) {
        		$this->vrac = $this->form->getUpdatedVrac();
        	} else {
        		throw new sfException($this->form->renderGlobalErrors());
        	}
		}
		$this->getUser()->setAttribute('vrac_object', serialize($this->vrac));
		return $this->redirect('annuaire_commercial_ajouter');
	}

	public function executeCloture(sfWebRequest $request)
	{
        $this->vrac = $this->getRoute()->getVrac();

        $this->secureVrac(VracSecurity::CLOTURE, $this->vrac);

		$this->validation = new VracValidation($this->vrac);
		if (!$this->vrac->allProduitsClotures() || $this->validation->hasErreurs()) {
            throw new sfError404Exception('Contrat vrac '.$this->vrac->_id.' n\'est pas cloturable.');
        }

		$this->vrac->clotureContrat();
		$this->vrac->save();

        VracMailer::getInstance()->sendMailsByStatutsChanged($this->vrac);

        $this->getUser()->setFlash('notice', 'Contrat cloturé avec succès. Un email a été envoyé à toutes les parties.');

        return $this->redirect('vrac_fiche', array('sf_subject' => $this->vrac));
	}

	public function executeForcerCloture(sfWebRequest $request)
	{
        $this->vrac = $this->getRoute()->getVrac();

        $this->secureVrac(VracSecurity::FORCE_CLOTURE, $this->vrac);

		$this->vrac->forceClotureContrat();
		$this->vrac->valide->email_cloture = true;
		$this->vrac->save();
		$this->getUser()->setFlash('notice', 'Contrat cloturé avec succès, aucun mail ne sera envoyé');

		return $this->redirect('vrac_fiche', array('sf_subject' => $this->vrac));
	}

	public function executeForcerValidation(sfWebRequest $request)
	{
        $this->vrac = $this->getRoute()->getVrac();

        $this->secureVrac(VracSecurity::FORCE_VALIDATION, $this->vrac);

		$this->vrac->signerAutomatiquement();
		$this->vrac->save();

		$this->getUser()->setFlash('notice', 'Contrat validé avec succès, aucun mail ne sera envoyé');

		return $this->redirect('vrac_fiche', array('sf_subject' => $this->vrac));
	}

	public function executeSupprimer(sfWebRequest $request)
	{
		$this->cleanSessions();
		$this->vrac = $this->getRoute()->getVrac();
        $this->forward404Unless(!$this->vrac->hasContratApplication());
        if (!$this->vrac->isApplicationPluriannuel()) {
            $this->secureVrac(VracSecurity::SUPPRESSION, $this->vrac);
        }

		if ($this->vrac->isNew()) {
            return $this->redirect('mon_espace_civa_vrac', $this->getUser()->getCompte());
		}

		$this->user = $this->getEtablissementCreateur();

		if (in_array($this->vrac->valide->statut, array(Vrac::STATUT_CREE))) {

			$this->vrac->delete();
            return $this->redirect('mon_espace_civa_vrac', $this->getUser()->getCompte());
		}

        if (in_array($this->vrac->valide->statut, array(Vrac::STATUT_PROJET_ACHETEUR, Vrac::STATUT_PROJET_VENDEUR))) {
            $this->vrac->motif_suppression = "Le projet a été supprimé par son créateur";
            foreach(VracMailer::getInstance()->refusApplication($this->vrac) as $message) {
                $this->getMailer()->send($message);
            }

			$this->vrac->delete();
            return $this->redirect('mon_espace_civa_vrac', $this->getUser()->getCompte());
		}

		if ($this->vrac->isApplicationPluriannuel() && $this->vrac->valide->statut == Vrac::STATUT_VALIDE_PARTIELLEMENT) {
            $vracCadre = $this->vrac->getContratPluriannuelCadre();
            foreach(VracMailer::getInstance()->refusApplication($this->vrac) as $message) {
                $this->getMailer()->send($message);
            }

			$this->vrac->delete();

            return $this->redirect('vrac_fiche', array('sf_subject' => $vracCadre));
        }

		$this->form = new VracSuppressionForm($this->vrac);

		if ($request->isMethod(sfWebRequest::POST)) {
    		$this->form->bind($request->getParameter($this->form->getName()));
        	if ($this->form->isValid()) {
        		$this->vrac = $this->form->save();

                VracMailer::getInstance()->sendMailsByStatutsChanged($this->vrac);

				return $this->redirect('vrac_fiche', array('sf_subject' => $this->vrac));
        	}
        }
    }

	public function executeFiche(sfWebRequest $request)
	{
		$this->cleanSessions();
        $this->vrac = $this->getRoute()->getVrac();
        $this->secureVrac(VracSecurity::CONSULTATION, $this->vrac);

        $this->user = $this->getTiersOfVrac($this->vrac);

		$this->form = $this->getFormRetiraisons($this->vrac, $this->user);
		$this->validation = new VracValidation($this->vrac);

        $this->contratsApplication = $this->vrac->getContratDeReference()->getContratsApplication();

    	if ($request->isMethod(sfWebRequest::POST)) {

            $this->secureVrac(VracSecurity::ENLEVEMENT, $this->vrac);

    		$this->form->bind($request->getParameter($this->form->getName()));
        	if ($this->form->isValid()) {
        		$vrac = $this->form->save();
				$this->getUser()->setFlash('notice', 'Le contrat a été mis à jour avec succès.');
       			return $this->redirect('vrac_fiche', array('sf_subject' => $vrac));
        	}
        }

        try {
            $application = $this->vrac->getContratDeReference()->generateNextPluriannuelApplication();
            $this->formApplication = $this->getForm($application, VracEtapes::ETAPE_PRODUITS);
			$this->validationApplication = new VracContratValidation($application);
        } catch (Exception $e) {
		    $this->formApplication = null;
            $this->validationApplication = null;
        }
    }


	public function executeMercurialeEdit(sfWebRequest $request)
	{
        $this->vrac = $this->getRoute()->getVrac();
        $this->forward404Unless($this->vrac);
        $this->forward404Unless($this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN));
        $this->form = new VracMercurialeForm($this->vrac);
        if ($request->isMethod(sfWebRequest::POST)) {
		    $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->save();
                $this->getUser()->setFlash('notice', 'Le contrat a été mis à jour avec succès.');
                return $this->redirect('vrac_fiche', array('sf_subject' => $this->vrac));
            }
        }
	}

	public function executeGenererContratApplication(sfWebRequest $request)
	{
        $contratPluriannuel = $this->getRoute()->getVrac();
        $this->forward404Unless($contratPluriannuel);
        $user = $this->getTiersOfVrac($contratPluriannuel);
        $this->forward404Unless($user);
        $this->forward404Unless($user->_id == $contratPluriannuel->createur_identifiant);
        $campagne = $request->getParameter('campagne');
        try {
            $nextContratApplication = $contratPluriannuel->generateNextPluriannuelApplication();
        } catch (Exception $e) {
		    $nextContratApplication = null;
        }
        $this->forward404Unless($nextContratApplication);
        $this->forward404Unless($nextContratApplication->numero_contrat == $contratPluriannuel->numero_contrat.$campagne);

        $this->form = $this->getForm($nextContratApplication, VracEtapes::ETAPE_PRODUITS);
        $this->validation = new VracContratValidation($nextContratApplication);
        $this->forward404Unless(!$this->validation->hasPoints());

    	if (!$request->isMethod(sfWebRequest::POST)) {

            throw new sfError404Exception();
        }

        $this->form->bind($request->getParameter($this->form->getName()));

        if (!$this->form->isValid()) {

            $this->getUser()->setFlash('notice', 'Une erreur est survenue lors de la génération du contrat d\'application');
            return $this->redirect('vrac_fiche', array('sf_subject' => $contratPluriannuel));
        }

		$nextContratApplication = $this->form->save();
        $nextContratApplication->createApplication($nextContratApplication->createur_identifiant);
        $nextContratApplication->save();

        VracMailer::getInstance()->sendMailsByStatutsChanged($nextContratApplication);

        if($nextContratApplication->isValide()) {
            $this->getUser()->setFlash('notice', 'Le contrat d\'application '.$campagne.' adossé au contrat pluriannuel visa n° '.$contratPluriannuel->numero_contrat.' a été généré avec succès. Il est validé. Un email va être envoyé à toutes les parties.');
        } else {
            $this->getUser()->setFlash('notice', 'Le contrat d\'application '.$campagne.' adossé au contrat pluriannuel visa n° '.$contratPluriannuel->numero_contrat.' a été généré avec succès. Il est en attente de signature des autres parties. Un email va leur être envoyé.');
        }

		return $this->redirect('vrac_fiche', array('sf_subject' => $nextContratApplication));
	}

    protected function getTiersOfVrac($vrac) {
        $tiers = $this->getUser()->getDeclarantsVrac();
        $user = null;

        foreach($tiers as $t) {
            if($vrac->isActeur($t->_id)) {
                $user = $t;
                break;
            }
        }

        return $user;
    }

	protected function getTiersNoSigneOfVrac($vrac) {
        $tiers = $this->getUser()->getDeclarantsVrac();
        $user = null;

        foreach($tiers as $t) {
            if($vrac->isActeur($t->_id) && !$vrac->hasSigne($t->_id)) {
                $user = $t;
            }
        }

        return $user;
    }

	public function executeValidation(sfWebRequest $request)
	{
		$this->cleanSessions();
		$this->vrac = $this->getRoute()->getVrac();
        $this->secureVrac(VracSecurity::SIGNATURE, $this->vrac);

		$this->user = $this->getTiersNoSigneOfVrac($this->vrac);

		$this->vrac->signer($this->user->_id);
		$this->vrac->save();

		$this->getUser()->setFlash('notice', 'Votre signature a bien été prise en compte. Un email de confirmation va vous être envoyé.');

        if ($this->vrac->valide->statut == Vrac::STATUT_PROPOSITION) {
            $this->getUser()->setFlash('notice', 'Votre signature a bien été prise en compte. Un email va être envoyé aux autres parties pour qu\'elles signent la proposition.');
        }

        if($this->vrac->isValide()) {
            $this->getUser()->setFlash('notice', 'Votre signature a bien été prise en compte. Le contrat est maintenant validé, il a été signé par toutes les parties. Un email va être envoyé à tout le monde.');
        }

        VracMailer::getInstance()->sendMailsByStatutsChanged($this->vrac);

		return $this->redirect('vrac_fiche', array('sf_subject' => $this->vrac));
    }

    public function executeEtape(sfWebRequest $request)
    {
		$this->user = $this->getEtablissementCreateur();
    	$this->etapes = VracEtapes::getInstance();
    	$this->etape = $request->getParameter('etape');
    	$this->referer = ($this->getUser()->getFlash('referer'))? 1 : 0;
    	$this->forward404Unless($this->etapes->exist($this->etape), 'L\'étape "'.$this->etape.'" n\'est pas prise en charge.');
		$this->vrac = $this->populateVracTiers($this->getRoute()->getVrac());
        $this->secureVrac(VracSecurity::EDITION, $this->vrac);

    	if ($this->etapes->isGt($this->etape, VracEtapes::ETAPE_PRODUITS) && !$this->vrac->hasProduits()) {
    		return $this->redirect('vrac_etape', array('sf_subject' => $this->vrac, 'etape' => VracEtapes::ETAPE_PRODUITS));
    	}
		$this->annuaire = $this->getAnnuaire();
    	$this->form = $this->getForm($this->vrac, $this->etape, $this->annuaire);
    	$this->next_etape = null;
    	if ($nextEtape = $this->getEtapeSuivante($this->etape, $this->etapes)) {
    		$this->next_etape = $this->vrac->etape = $nextEtape;
    	}
		$this->validation = new VracContratValidation($this->vrac, $this->annuaire);
    	if (!$request->isMethod(sfWebRequest::POST)) {

            return sfView::SUCCESS;
        }
        $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
        if (!$this->form->isValid()) {

            return sfView::SUCCESS;
        }

		$this->form->save();
		if ($request->isXmlHttpRequest()) {
			return sfView::NONE;
		}
    	$this->cleanSessions();

		if ($nextEtape) {

            return $this->redirect('vrac_etape', array('sf_subject' => $this->vrac, 'etape' => $request->hasParameter('submitAndReload') ? $this->etape : $this->vrac->etape));
		}

        VracMailer::getInstance()->sendMailsByStatutsChanged($this->vrac);

        if($this->vrac->isPapier()) {
            $this->getUser()->setFlash('notice', 'Le contrat papier a été créé avec succès. Chacun des acteurs du contrat va recevoir un mail de confirmation contenant le numéro de visa.');
        }

        if ($this->vrac->valide->statut == Vrac::STATUT_PROJET_VENDEUR) {
            $this->getUser()->setFlash('notice', 'Le projet a été créé avec succès. Celui-ci a été transmis à l\'acheteur ou au courtier afin qu\'il le valide.');
        }

        if ($this->vrac->valide->statut == Vrac::STATUT_PROJET_ACHETEUR) {
            $this->getUser()->setFlash('notice', 'Le projet a été transmis au vendeur afin qu\'il le signe.');
        }

    	return $this->redirect('vrac_fiche', array('sf_subject' => $this->vrac));
    }

    public function executeAjouterProduit(sfWebRequest $request)
    {

        $this->user = $this->getEtablissementCreateur();
        $this->config = ConfigurationClient::getConfiguration('2012');
        //$this->appellationsLieuDit = json_encode($this->config->getAppellationsLieuDit());
        $this->appellationsLieuDit = json_encode(array());
        $this->vrac = $this->getRoute()->getVrac();

        $this->secureVrac(VracSecurity::EDITION, $this->vrac);

    	$this->etapes = VracEtapes::getInstance();
    	$this->etape = $request->getParameter('etape');
    	$this->forward404Unless($this->etapes->exist($this->etape), 'L\'étape "'.$this->etape.'" n\'est pas prise en charge.');
    	$this->form = new VracProduitAjoutForm($this->vrac);
    	if ($request->isMethod(sfWebRequest::POST)) {
    		$this->form->bind($request->getParameter($this->form->getName()));
        	if ($this->form->isValid()) {
       			$this->form->save();
       			$this->getUser()->setFlash('referer', 'ajout-produit');
       			return $this->redirect('vrac_etape', array('sf_subject' => $this->vrac, 'etape' => $this->etape));
        	}
        }
    }

    public function executeSoussigneInformations(sfWebRequest $request)
    {

    	if (!$request->isXmlHttpRequest()) {
    		throw new sfException('Requête ajax obligatoire.');
    	}
    	$identifiant = $request->getParameter('identifiant', null);
    	if (!$identifiant) {
    		throw new sfException('Id du tiers obligatoire.');
    	}
    	$etablissement = EtablissementClient::getInstance()->find($identifiant);
    	if (!$etablissement) {
    		throw new sfException('Le tiers d\'id "'.$identifiant.'" n\'existe pas.');
    	}

        $acteur = $request->getParameter('acteur');
        $this->vrac = $this->populateVracTiers($this->getRoute()->getVrac());

        $this->secureVrac(VracSecurity::EDITION, $this->vrac);

        $this->vrac->addActeur($acteur, $etablissement);

    	return $this->renderPartial('vrac/soussigne', array('vrac' => $this->vrac, 'tiers' => $this->vrac->{$acteur}, 'fiche' => false));
    }

    public function executeAjouterProduitLieux(sfWebRequest $request)
    {
    	if (!$request->isXmlHttpRequest()) {
    		throw new sfException('Requête ajax obligatoire.');
    	}
    	$appellation = $request->getParameter('appellation', null);
    	if (!$appellation) {
    		throw new sfException('Appellation obligatoire.');
    	}
    	$this->config = ConfigurationClient::getConfiguration('2012');
    	if (!$this->config->exist($appellation)) {
    		throw new sfException('Appellation "'.$appellation.'" n\'existe pas.');
    	}
    	$result = array();
    	if ($this->config->get($appellation)->hasManyLieu()) {
			foreach ($this->config->get($appellation)->getLieux() as $key => $lieu) {
				$result[$lieu->getKey()] = $lieu->libelle;
			}
    	}
    	return $this->renderText(json_encode($result));
    }

    public function executeAjouterProduitCepages(sfWebRequest $request)
    {
    	if (!$request->isXmlHttpRequest()) {
    		throw new sfException('Requête ajax obligatoire.');
    	}
    	$appellation = $request->getParameter('appellation', null);
    	$lieu = $request->getParameter('lieu', 'lieu');
    	if (!$lieu) {
    		$lieu = 'DEFAUT';
    	}
    	if (!$appellation) {
    		throw new sfException('Appellation obligatoire.');
    	}
    	$this->config = ConfigurationClient::getConfiguration('2012');
    	if (!$this->config->exist($appellation)) {
    		throw new sfException('Appellation "'.$appellation.'" n\'existe pas.');
    	}
    	if (!$this->config->get($appellation)->mentions->get('DEFAUT')->lieux->exist($lieu)) {
    		throw new sfException('Lieu "'.$lieu.'" n\'existe pas.');
    	}
    	$result = array();
		foreach ($this->config->get($appellation)->mentions->get('DEFAUT')->lieux->get($lieu)->getProduits() as $key => $cepage) {
			$result[str_replace('/recolte/', 'declaration/', $cepage->getHash())] = $cepage->libelle;
			if ($key == Vrac::CEPAGE_EDEL) {
				$result[str_replace('/recolte/', 'declaration/', $cepage->getHash())] = $result[str_replace('/recolte/', 'declaration/', $cepage->getHash())].Vrac::CEPAGE_EDEL_LIBELLE_COMPLEMENT;
			}
			if ($key == Vrac::CEPAGE_MUSCAT) {
				$result[str_replace('/recolte/', 'declaration/', $cepage->getHash())] = Vrac::CEPAGE_MUSCAT_LIBELLE;
			}
                        if (($appellation == 'appellation_'.Vrac::APPELLATION_PINOTNOIRROUGE) &&  ($key == Vrac::CEPAGE_PR)) {
				$result[str_replace('/recolte/', 'declaration/', $cepage->getHash())] = $result[str_replace('/recolte/', 'declaration/', $cepage->getHash())].Vrac::CEPAGE_PR_LIBELLE_COMPLEMENT;
			}
            if ($cepage->getAppellation()->getKey() == Vrac::APPELLATION_CREMANT && in_array($cepage->getKey(), Vrac::$cepages_exclus_cremant)) {
                unset($result[str_replace('/recolte/', 'declaration/', $cepage->getHash())]);
            }
		}
    	return $this->renderText(json_encode($result));
    }

    public function executeAjouterProduitVtsgn(sfWebRequest $request)
    {
    	if (!$request->isXmlHttpRequest()) {
    		throw new sfException('Requête ajax obligatoire.');
    	}
    	$hash = $request->getParameter('hash', null);
    	if (!$hash) {
    		throw new sfException('Hash cépage obligatoire.');
    	}
    	$this->config = ConfigurationClient::getConfiguration('2012');
    	if (!$this->config->exist($hash)) {
    		throw new sfException('Cépage "'.$hash.'" n\'existe pas.');
    	}
    	$cepage = $this->config->get($hash);
    	return $this->renderText($cepage->hasVtsgn());
    }

    public function executeDownloadNotice() {

        return $this->renderPdf(sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . "helpPdf/aide_vrac.pdf", "aide_contrat.pdf");
    }

    protected function renderPdf($path, $filename) {
        $this->getResponse()->setHttpHeader('Content-Type', 'application/pdf');
        $this->getResponse()->setHttpHeader('Content-disposition', 'attachment; filename="' . $filename . '"');
        $this->getResponse()->setHttpHeader('Content-Transfer-Encoding', 'binary');
        $this->getResponse()->setHttpHeader('Content-Length', filesize($path));
        $this->getResponse()->setHttpHeader('Pragma', '');
        $this->getResponse()->setHttpHeader('Cache-Control', 'public');
        $this->getResponse()->setHttpHeader('Expires', '0');
        return $this->renderText(file_get_contents($path));
    }

	protected function getForm(Vrac $vrac, $etape, $annuaire = null)
	{
		return VracFormFactory::create($vrac, $etape, $annuaire);
	}

    protected function getEtapeSuivante($etape, $etapes)
    {
    	$next = null;
    	$nextEtape = $etapes->getNext($etape);
    	if ($nextEtape && $etapes->isLt($etape, $nextEtape)) {
    		$next = $nextEtape;
    	}
    	return $next;
    }

    protected function getAnnuaire()
    {
    	$compte = $this->getUser()->getCompte();
		return AnnuaireClient::getInstance()->findOrCreateAnnuaire($compte->login);
    }

    protected function getFormRetiraisons($vrac, $user)
    {
		if(!$vrac->needRetiraison()) {
			return null;
		}
		if($vrac->isPluriannuelCadre()) {

			return null;
		}

    	if ($vrac->isValide() && !$vrac->isCloture() && $vrac->isProprietaire($user->_id) && !$vrac->isAnnule()) {
    		return new VracProduitsEnlevementsForm($vrac);
    	}
    	return null;
    }

    protected function getCampagnes($vracs, $courante)
    {
        $campagnes = array($courante);
        foreach ($vracs as $vrac) {
            if (!in_array($vrac->key[2], $campagnes)) {
                $campagnes[] = $vrac->key[2];
            }
        }
        rsort($campagnes);
        return $campagnes;
    }

    protected function getStatuts()
    {
        $statuts = Vrac::getStatutsLibelles();
        $statuts[Vrac::STATUT_VALIDE_PARTIELLEMENT] = $statuts[Vrac::STATUT_VALIDE_PARTIELLEMENT].'/signature';
        $statuts[Vrac::STATUT_VALIDE] = $statuts[Vrac::STATUT_VALIDE].' / À enlever';
        return $statuts;
    }

    protected function secureVrac($droits, $vrac) {
		if(!isset($this->compte)) {
			$this->compte = $this->getUser()->getCompte();
		}
        if(!VracSecurity::getInstance($this->compte, $vrac)->isAuthorized($droits)) {

            return $this->forwardSecure();
        }
    }

    protected function forwardSecure()
    {
        $this->context->getController()->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));

        throw new sfStopException();
    }

    protected function cleanSessions()
    {
    	$this->getUser()->setAttribute('vrac_object', null);
    	$this->getUser()->setAttribute('vrac_acteur', null);
    	$this->getUser()->setAttribute('vrac_type_tiers', null);
    	$this->getUser()->setAttribute('vrac_createur', null);
    	$this->getUser()->setAttribute('vrac_papier', null);
    }

	protected function getEtablissementCreateur() {
		if($this->getUser()->getAttribute('vrac_createur')) {
			$declarant = EtablissementClient::getInstance()->find($this->getUser()->getAttribute('vrac_createur'));
		} else {
			$declarant = VracClient::getInstance()->getFirstEtablissement($this->getUser()->getCompte()->getSociete());
		}

		return $declarant;
	}

    protected function populateVracTiers($vrac)
    {
		$declarant = $this->getEtablissementCreateur();

    	$typeTiers = $this->getUser()->getAttribute('vrac_type_tiers');

        if(in_array($declarant->getFamille(), array(EtablissementFamilles::FAMILLE_PRODUCTEUR, EtablissementFamilles::FAMILLE_PRODUCTEUR_VINIFICATEUR))) {
            $typeTiers = 'vendeur';
        }

        if(in_array($declarant->getFamille(), array(EtablissementFamilles::FAMILLE_COURTIER))) {
            $typeTiers = 'mandataire';
        }

        if(!$typeTiers) {
            $typeTiers = 'acheteur';
        }

    	if ($vrac->isNew() && $typeTiers == 'vendeur') {
    		$vrac->vendeur_identifiant = $declarant->_id;
            $vrac->storeVendeurInformations($declarant);
            $vrac->setVendeurQualite($declarant->getFamille());
    	} elseif($vrac->isNew() && $typeTiers == 'acheteur') {
    		$vrac->acheteur_identifiant = $declarant->_id;
            $vrac->storeAcheteurInformations($declarant);
            $vrac->setAcheteurQualite($declarant->getFamille());
		}

		return $vrac;
    }

	public function executeRefuserProjet(sfWebRequest $request)
	{
		$this->cleanSessions();
		$vrac = $this->getRoute()->getVrac();
        $user = $this->getTiersNoSigneOfVrac($vrac);
        $vrac->refusProjet($user->_id);
        $vrac->save();

        VracMailer::getInstance()->sendMailsByStatutsChanged($vrac);

        $this->getUser()->setFlash('notice', "Le refus du projet a été notifié par mail à l'autre partie.");
		return $this->redirect('vrac_fiche', array('sf_subject' => $vrac));
    }

	public function executeAnnexe(sfWebRequest $request)
	{
		$vrac = $this->getRoute()->getVrac();
    	$annexe = $request->getParameter('annexe', null);
    	if (!$annexe) {
    		throw new sfException('annexe obligatoire.');
    	}
    	$operation = $request->getParameter('operation', null);
    	if (!$operation) {
    		throw new sfException('operation obligatoire.');
    	}
		if ($operation == 'supprimer') {
            if ($vrac->deleteAnnexe($annexe)) {
                $vrac->save();
            }
		}
		if (($filename = $vrac->getAnnexeFilename($annexe)) && $operation == 'visualiser') {
            $this->getResponse()->setHttpHeader('Content-disposition', 'attachment; filename="' . $filename . '"');
            return $this->renderText(file_get_contents($vrac->getAttachmentUri($filename)));exit;
		}
        return $this->redirect('vrac_etape', array('sf_subject' => $vrac, 'etape' => VracEtapes::ETAPE_ANNEXES));
    }

}
