<?php

/**
 * drm actions.
 *
 * @package    vinsi
 * @subpackage drm
 * @author     Mathurin
 */
class drmActions extends drmGeneriqueActions {

    public function executeConnexion(sfWebRequest $request) {

        //  $this->redirect403IfIsTeledeclaration();
        $this->etablissement = $this->getRoute()->getEtablissement();
        $societe = $this->etablissement->getSociete();

        $this->getUser()->usurpationOn($societe->identifiant, $request->getReferer());
        $this->redirect('drm_societe', array('identifiant' => $societe->getEtablissementPrincipal()->identifiant));
    }

    public function executeRedirect(sfWebRequest $request) {
        $drm = DRMClient::getInstance()->find($request->getParameter('identifiant_drm'));
        $this->forward404Unless($drm);
        return $this->redirect('drm_visualisation', array('identifiant' => $drm->identifiant, 'periode_version' => $drm->getPeriodeAndVersion()));
    }

    public function executeIndex(sfWebRequest $request) {
        //$this->redirect403IfIsTeledeclaration();
    }

    public function executeEtablissementSelection(sfWebRequest $request) {
        //$this->redirect403IfIsTeledeclaration();

        $form = new DRMEtablissementChoiceForm('INTERPRO-declaration');
        $form->bind($request->getParameter($form->getName()));
        if (!$form->isValid()) {

            return $this->redirect('drm');
        }

        return $this->redirect('drm_etablissement', $form->getEtablissement());
    }

    public function executeRedirectEtape(sfWebRequest $request) {
        $isTeledeclarationMode = $this->isTeledeclarationDrm();
        $drm = $this->getRoute()->getDRM();

        switch ($drm->etape) {
            case DRMClient::ETAPE_CHOIX_PRODUITS:
                if ($isTeledeclarationMode) {
                    return $this->redirect('drm_choix_produit', array('identifiant' => $drm->identifiant, 'periode_version' => $drm->getPeriodeAndVersion()));
                } else {
                    return $this->redirect('drm_edition', array('identifiant' => $drm->identifiant, 'periode_version' => $drm->getPeriodeAndVersion()));
                }
                break;

            case DRMClient::ETAPE_SAISIE:
                return $this->redirect('drm_edition', array('identifiant' => $drm->identifiant, 'periode_version' => $drm->getPeriodeAndVersion()));
                break;

            case DRMClient::ETAPE_CRD:
                if ($isTeledeclarationMode) {
                    return $this->redirect('drm_crd', array('identifiant' => $drm->identifiant, 'periode_version' => $drm->getPeriodeAndVersion()));
                } else {
                    return $this->redirect('drm_validation', array('identifiant' => $drm->identifiant, 'periode_version' => $drm->getPeriodeAndVersion()));
                }
                break;

            case DRMClient::ETAPE_ADMINISTRATION:
                if ($isTeledeclarationMode) {
                    return $this->redirect('drm_annexes', array('identifiant' => $drm->identifiant, 'periode_version' => $drm->getPeriodeAndVersion()));
                } else {
                    return $this->redirect('drm_validation', array('identifiant' => $drm->identifiant, 'periode_version' => $drm->getPeriodeAndVersion()));
                }
                break;

            case DRMClient::ETAPE_VALIDATION:
                return $this->redirect('drm_validation', array('identifiant' => $drm->identifiant, 'periode_version' => $drm->getPeriodeAndVersion()));
                break;
        }

        if ((!$drm->etape) && !$drm->isValidee()) {
            return $this->redirect('drm_edition', array('identifiant' => $drm->identifiant, 'periode_version' => $drm->getPeriodeAndVersion()));
        }

        return $this->redirect('drm_visualisation', array('identifiant' => $drm->identifiant, 'periode_version' => $drm->getPeriodeAndVersion()));
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeChoixCreation(sfWebRequest $request) {
        $isTeledeclarationMode = $this->isTeledeclarationDrm();
        if ($request->isMethod(sfWebRequest::POST)) {
            if (!$request->getParameter('drmChoixCreation')) {
                new sfException("Le formulaire n'est pas valide");
            }
            $drmChoixCreation = $request->getParameter('drmChoixCreation');
            $choixCreation = $drmChoixCreation['type_creation'];
            $identifiant = $request->getParameter('identifiant');
            $periode = $request->getParameter('periode');
            $this->creationDrmForm = new DRMChoixCreationForm(array(), array('identifiant' => $identifiant, 'periode' => $periode));
            $this->creationDrmForm->bind($request->getParameter($this->creationDrmForm->getName()), $request->getFiles($this->creationDrmForm->getName()));

            switch ($choixCreation) {
                case DRMClient::DRM_CREATION_EDI :
                    //if ($this->creationDrmForm->isValid()) {
                        $md5 = $this->creationDrmForm->getValue('file')->getMd5();

                        return $this->redirect('drm_verification_fichier_edi', array('identifiant' => $identifiant, 'periode' => $periode, 'md5' => $md5));
                    //}

                    return $this->redirect('drm_societe', array('identifiant' => $identifiant));

                    break;
                case DRMClient::DRM_CREATION_VIERGE :
                    return $this->redirect('drm_nouvelle', array('identifiant' => $identifiant, 'periode' => $periode));
                    break;
                case DRMClient::DRM_CREATION_NEANT :
                    $drm = DRMClient::getInstance()->createDoc($identifiant, $periode, $isTeledeclarationMode);
                    $drm->etape = DRMClient::ETAPE_VALIDATION;
                    $drm->type_creation = DRMClient::DRM_CREATION_NEANT;
                    $drm->save();
                    return $this->redirect('drm_validation', array('identifiant' => $drm->identifiant, 'periode_version' => $drm->getPeriodeAndVersion()));
                    break;
                case DRMClient::DRM_CREATION_DOCUMENTS :
                    /* Ici on fait la reprise et l'alimentation de la DRM par EDI
                     * La route drm_create_from_documents créer une DRM et la remplie avec des éléments CATALOGUE ou MOUVEMENT
                     * provenant d'un fichier EDI créé
                     */
                    return $this->redirect('drm_create_from_documents', array('identifiant' => $identifiant, 'periode' => $periode));
                    break;
            }
        }
        return $this->redirect('drm_societe', array('identifiant' => $identifiant));
    }

    /**
     * Route de création de DRM et de fichier EDI venant alimenter la DRM
     * @param sfWebRequest $request
     */
    public function executeCreateEdiFileFromDocuments(sfWebRequest $request) {
        $allowIps = array("127.0.0.1","localhost");
        if(!in_array($this->getRequest()->getHttpHeader ('addr','remote'),$allowIps)){
          throw new sfException("Accès interdit");

        }
        $isTeledeclarationMode = $this->isTeledeclarationDrm();
        $identifiant = $request->getParameter('identifiant');
        $periode = $request->getParameter('periode');

        $drm = new DRM();
        $drm->identifiant = $identifiant;
        $drm->periode = $periode;

        $ediFileContent = "";

        /**
        * Cas d'initialisation : on récupère les informations CATALOGUE produit depuis le doc le plus récent entre DS et DR
        */
        $documentRepriseInfos = DRMClient::getInstance()->getDocumentsForRepriseCatalogue($identifiant, $periode);
        foreach ($documentRepriseInfos as $documentRepriseInfo) {
        $ediFileContent.= $this->createReprise($documentRepriseInfo,$drm);
        }

         /**
          * Dans tout les cas de figure, après la DRM crée, on récupère des mouvements/stock :
          *  - Si la DRM est juste après la DR, on récupère les mouvements d'entrées récolte
          *  - Si la DRM est juste après une DS, on récupère les stocks début de mois
          *  - Dans tout les cas, on récupère les mouvements de sortie contrat en phase d'enlèvement
          */
        $repriseMvtInfos = DRMClient::getInstance()->getDocumentsForRepriseMouvements($identifiant, $periode);

        foreach ($repriseMvtInfos as $repriseMvtInfo) {
          $ediFileContent.= $this->createReprise($repriseMvtInfo,$drm);
        }
        if($ediFileContent){
            $this->response->setContentType('text/csv');

            echo $ediFileContent;
            exit;

          }else{
            return $this->redirect('drm_choix_produit', $drm);
          }
      }


      /**
     *
     * @param sfWebRequest $request
     */
    public function executeImportEdiFileFromDocuments(sfWebRequest $request) {
        $this->isTeledeclarationMode = $this->isTeledeclarationDrm();
        $this->identifiant = $request->getParameter('identifiant');
        $this->periode = $request->getParameter('periode_version');
        $filediscr = $request->getParameter('filediscr');

        $filename = 'import_'.$this->identifiant . '_' . $this->periode.'_'.$filediscr.'.csv';
        $path = sfConfig::get('sf_data_dir') . '/upload/'.$filename;
        $this->md5 = $filediscr;
        $this->drm = $this->getRoute()->getDRM();

        $this->drmCsvEdi = new DRMImportCsvEdiPartial($path,$this->drm);

        $this->drmCsvEdi->checkCSV();

        if($this->drmCsvEdi->hasErreurs()){
          return $this->setTemplate("verificationEdi");
        }else{
          $this->drmCsvEdi->importCSV(true);
          unlink($path);
          return $this->redirect('drm_choix_produit', $this->drm);
        }
    }


    private function createReprise($documentRepriseInfo,$drm){
      $docTypeClientName = $documentRepriseInfo->docType.'Client';
      $docTypeClient = $docTypeClientName::getInstance();
      $doc = $docTypeClient->find($documentRepriseInfo->idDoc);
      $edi = new DRMExportCsvEdi($drm);
      $ediFileUpdate = "";
      if(!$documentRepriseInfo->viewResult){
        switch ($documentRepriseInfo->repriseType) {
          case DRMClient::REPRISE_TYPE_CATALOGUE :
          $ediFileUpdate.=$doc->getDRMEdiProduitRows($edi);
          break;
          case DRMClient::REPRISE_TYPE_MOUVEMENT :
          $ediFileUpdate.=$doc->getDRMEdiMouvementRows($edi);
          break;
        }
      }else{
        $viewresult = $documentRepriseInfo->viewResult;
        $produitHash = $viewresult->value[DRMRepriseMvtsView::VALUE_PRODUIT_HASH];
        $catMouvement = $viewresult->value[DRMRepriseMvtsView::VALUE_CAT_MVT];
        $typeMouvement = $viewresult->value[DRMRepriseMvtsView::VALUE_TYPE_MVT];
        $volume = $viewresult->value[DRMRepriseMvtsView::VALUE_VOLUME];

        $cepageNode = $doc->getOrAdd($produitHash);
        foreach ($cepageNode->getProduitsDetails()  as $key => $produitsDetail) {
          foreach ($produitsDetail->getRetiraisons() as $retiraison) {
            if(substr(str_replace("-",'',$retiraison->getDate()),0,6) == $drm->getPeriode()){
              $ediFileUpdate .= $edi->createRowMouvementProduitDetail($produitsDetail, $catMouvement,$typeMouvement,$volume,$documentRepriseInfo->idDoc);
            }
          }
        }
      }
      return $ediFileUpdate;
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeVerificationEdi(sfWebRequest $request) {

        $this->md5 = $request->getParameter('md5');
        $this->identifiant = $request->getParameter('identifiant');
        $this->periode = $request->getParameter('periode');

        $this->drm = new DRM();
        $this->drm->identifiant = $this->identifiant;
        $this->drm->periode = $this->periode;
        $this->drm->teledeclare = true;

        $this->drmCsvEdi = new DRMImportCsvEdi(sfConfig::get('sf_data_dir') . '/upload/' . $this->md5, $this->drm);
        $this->drmCsvEdi->checkCSV();

    }

        /**
     *
     * @param sfWebRequest $request
     */
    public function executeCreationEdi(sfWebRequest $request) {

        $this->md5 = $request->getParameter('md5');
        $this->identifiant = $request->getParameter('identifiant');
        $this->periode = $request->getParameter('periode');
        $filename = 'import_'.$this->identifiant . '_' . $this->periode.'_'.$this->md5.'.csv';
        $this->drm = new DRM();
        $this->drm->identifiant = $this->identifiant;
        $this->drm->periode = $this->periode;
        $this->drm->teledeclare = true;

        $this->drmCsvEdi = new DRMImportCsvEdi(sfConfig::get('sf_data_dir') . '/upload/' . $filename, $this->drm);
        $this->drmCsvEdi->importCSV();

        $this->redirect('drm_validation', $this->drm);

    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeExportEdi(sfWebRequest $request) {
        $this->setLayout(false);
        $drm = $this->getRoute()->getDRM();

        $this->drmCsvEdi = new DRMExportCsvEdi($drm);

        $filename = $drm->identifiant . '_' . $drm->periode.'_'.$drm->_rev.'.csv';


        $attachement = "attachment; filename=" . $filename . ".csv";

        $this->response->setContentType('text/csv');
        $this->response->setHttpHeader('Content-Disposition', $attachement);

    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeNouvelle(sfWebRequest $request) {
        $isTeledeclarationMode = $this->isTeledeclarationDrm();
        $identifiant = $request->getParameter('identifiant');
        $periode = $request->getParameter('periode');
        $drm = DRMClient::getInstance()->createDoc($identifiant, $periode, $isTeledeclarationMode);
        //$drm->loadAllProduits();
        $drm->save();
        if ($isTeledeclarationMode) {
            $this->redirect('drm_choix_produit', $drm);
        } else {
            $this->redirect($this->generateUrl('drm_edition', $drm).'#col_saisies_cont');
        }
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeInProcess(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->campagne = $request->getParameter('campagne');
        $this->historique = new DRMHistorique($request->getParameter('identifiant'), $this->campagne);
        if (!$this->campagne) {
            $this->campagne = '2012-2013';
        }
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeDelete(sfWebRequest $request) {
        $this->isTeledeclarationMode = $this->isTeledeclarationDrm();
        $this->drm = $this->getRoute()->getDRM();
        $identifiant = $this->drm->getidentifiant();
        $this->initDeleteForm();
        if ($request->isMethod(sfRequest::POST)) {
            $this->deleteForm->bind($request->getParameter($this->deleteForm->getName()));
            if ($this->deleteForm->isValid()) {
                $this->drm->delete();
                $url = $this->generateUrl('drm_etablissement', array('identifiant' => $identifiant, 'campagne' => -1));
                $this->redirect($url);
            }
        }
    }

    private function formCampagne(sfWebRequest $request, $route) {
        //$this->isTeledeclarationMode = $this->isTeledeclarationDrm();
        $this->isTeledeclarationMode = false;
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->societe = $this->etablissement->getSociete();
        if ($this->etablissement->famille != EtablissementFamilles::FAMILLE_PRODUCTEUR_VINIFICATEUR)
             throw new sfException("L'établissement sélectionné ne déclare pas de DRM");

        $this->campagne = $request->getParameter('campagne');
        if (!$this->campagne) {
            $this->campagne = -1;
        }

        $this->formCampagne = new DRMEtablissementCampagneForm($this->etablissement->identifiant, $this->campagne, $this->isTeledeclarationMode);
        if ($request->isMethod(sfWebRequest::POST)) {
            $param = $request->getParameter($this->formCampagne->getName());
            if ($param) {
                $this->formCampagne->bind($param);
                return $this->redirect($route, array('identifiant' => $this->etablissement->getIdentifiant(), 'campagne' => $this->formCampagne->getValue('campagne')));
            }
        }
    }

    /**
     * Executes mon espace action
     *
     * @param sfRequest $request A request object
     */
    public function executeMonEspace(sfWebRequest $request) {
        $view = $this->formCampagne($request, 'drm_etablissement');
        $this->calendrier = new DRMCalendrier($this->etablissement, $this->campagne, $this->isTeledeclarationMode);
        return $view;
    }

    public function executeStocks(sfWebRequest $request) {
        return $this->formCampagne($request, 'drm_etablissement_stocks');
    }

    /**
     * Executes historique action
     *
     * @param sfRequest $request A request object
     */
    public function executeHistorique(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->campagne = $request->getParameter('campagne');
    }

    /**
     * Executes informations action
     *
     * @param sfRequest $request A request object
     */
    public function executeInformations(sfWebRequest $request) {
        $this->drm = $this->getRoute()->getDRM();
        $this->tiers = $this->getUser()->getTiers();
        $isAdmin = $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN);
        $this->form = new DRMInformationsForm(array(), array('is_admin' => $isAdmin));

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {
                $values = $this->form->getValues();
                if ($values['confirmation'] == "modification") {
                    $this->redirect('drm_modif_infos', $this->drm);
                } elseif ($values['confirmation']) {
                    $this->drm->declarant->nom = $this->tiers->nom;
                    $this->drm->declarant->raison_sociale = $this->tiers->raison_sociale;
                    $this->drm->declarant->siret = $this->tiers->siret;
                    $this->drm->declarant->cni = $this->tiers->cni;
                    $this->drm->declarant->cvi = $this->tiers->cvi;
                    $this->drm->declarant->siege->adresse = $this->tiers->siege->adresse;
                    $this->drm->declarant->siege->code_postal = $this->tiers->siege->code_postal;
                    $this->drm->declarant->siege->commune = $this->tiers->siege->commune;
                    $this->drm->declarant->comptabilite->adresse = $this->tiers->comptabilite->adresse;
                    $this->drm->declarant->comptabilite->code_postal = $this->tiers->comptabilite->code_postal;
                    $this->drm->declarant->comptabilite->commune = $this->tiers->comptabilite->commune;
                    $this->drm->declarant->no_accises = $this->tiers->no_accises;
                    $this->drm->declarant->no_tva_intracommunautaire = $this->tiers->no_tva_intracommunautaire;
                    $this->drm->declarant->service_douane = $this->tiers->service_douane;
                    $this->drm->save();
                }
                $this->drm->setCurrentEtapeRouting('ajouts_liquidations');
                $this->redirect('drm_mouvements_generaux', $this->drm);
            }
        }
    }

    public function executeModificationInfos(sfWebRequest $request) {
        $this->drm = $this->getRoute()->getDRM();
    }

    public function executeDeclaratif(sfWebRequest $request) {
        $this->drm = $this->getRoute()->getDRM();
        $this->drm->setCurrentEtapeRouting('declaratif');
        $this->form = new DRMDeclaratifForm($this->drm);
        $this->hasFrequencePaiement = ($this->drm->declaratif->paiement->douane->frequence) ? true : false;
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->save();
                $this->drm->setCurrentEtapeRouting('validation');
                $this->redirect('drm_validation', $this->drm);
            }
        }
    }

    public function executePaiementFrequenceFormAjax(sfWebRequest $request) {
        $this->forward404Unless($request->isXmlHttpRequest());
        $drm = $this->getRoute()->getDRM();
        return $this->renderText($this->getPartial('popupFrequence', array('drm' => $drm)));
    }

    public function executeShowError(sfWebRequest $request) {
        $drm = $this->getRoute()->getDRM();
        $drmValidation = new DRMValidation($drm);
        $controle = $drmValidation->find($request->getParameter('type'), $request->getParameter('identifiant'));
        $this->forward404Unless($controle);
        $this->getUser()->setFlash('control_message', $controle->getMessage());
        $this->getUser()->setFlash('control_css', "flash_" . $controle->getType());
        $this->redirect($controle->getLien());
    }

    public function executeRectificative(sfWebRequest $request) {
        $drm = $this->getRoute()->getDRM();

        $drm_rectificative = $drm->generateRectificative();
        $drm_rectificative->save();

        return $this->redirect('drm_redirect_etape', array('identifiant' => $drm_rectificative->identifiant, 'periode_version' => $drm_rectificative->getPeriodeAndVersion()));
    }

    public function executeModificative(sfWebRequest $request) {
        $drm = $this->getRoute()->getDRM();

        $drm_rectificative = $drm->generateModificative();
        $drm_rectificative->save();

        return $this->redirect('drm_redirect_etape', array('identifiant' => $drm_rectificative->identifiant, 'periode_version' => $drm_rectificative->getPeriodeAndVersion()));
    }

    /**
     * Executes mouvements generaux action
     *
     * @param sfRequest $request A request object
     */
    public function executePdf(sfWebRequest $request) {

        ini_set('memory_limit', '512M');
        $this->drm = $this->getRoute()->getDRM();
        $pdf = new ExportDRMPdf($this->drm);

        return $this->renderText($pdf->render($this->getResponse(), false, $request->getParameter('format')));
    }

    public function executeSociete(sfWebRequest $request) {

        $this->identifiant = $request['identifiant'];

        $this->initSocieteAndEtablissementPrincipal();

        $this->redirect403IfIsNotTeledeclarationAndNotMe();

        $this->redirect('drm_etablissement', $this->etablissementPrincipal);
    }

}
