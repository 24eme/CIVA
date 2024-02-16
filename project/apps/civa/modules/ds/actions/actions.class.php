<?php
class dsActions extends sfActions {

    public function executeInit(sfWebRequest $request) {
       set_time_limit(240);
       ini_set("memory_limit", "256M");

       $this->forward404Unless($request->isMethod(sfWebRequest::POST));

       $type_ds = $request->getParameter("type");

       $this->etablissement = $this->getRoute()->getEtablissement();

       $this->periode = CurrentClient::getCurrent()->getPeriodeDSByType($type_ds);
       $this->ds = DSCivaClient::getInstance()->findPrincipaleByEtablissementAndPeriode($type_ds, $this->etablissement, $this->periode);
       $this->secureDS(array(DSSecurity::CONSULTATION,
                             DSSecurity::EDITION), $this->ds, $type_ds);

       $ds_data = $this->getRequestParameter('ds', null);

        if ($ds_data) {
            if ($ds_data['type_declaration'] == 'brouillon') {

                return $this->redirect('ds_etape_redirect', $this->ds);
            } elseif ($ds_data['type_declaration'] == 'supprimer') {
                DSCivaClient::getInstance()->removeDeclaration($this->ds);

                return  $this->redirect('mon_espace_civa_ds', array("type" => $type_ds, "identifiant" => $this->etablissement->getIdentifiant()));
            } elseif ($ds_data['type_declaration'] == 'visualisation') {

                return $this->redirect('ds_visualisation', $this->ds);
            }
        }
        $this->ds = null;
        if((count($request["boutons"]) < 1) || !isset($request["boutons"])){
            throw new sfException("Il semble que l'initialisation des ds n'est pas été effectuée depuis un bouton de validation.");
        }
        $ds_type_arr = $request["ds"]["type_declaration"];
        $ds_neant = ($ds_type_arr == 'ds_neant');
        $date = date(sprintf('%s-%s-%s', CurrentClient::getCurrent()->getAnneeDS($type_ds), CurrentClient::getCurrent()->getMonthDS($type_ds), '31'));
        $dss = DSCivaClient::getInstance()->findOrCreateDssByTiers($this->etablissement, $type_ds, $date, $ds_neant, true);
        foreach ($dss as $ds) {
            if($ds->isDsPrincipale() && $this->getUser()->hasCredential(CompteSecurityUser::CREDENTIAL_OPERATEUR) && !$this->getUser()->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)){
                $ds->add('num_etape',2);
                $ds->add('date_depot_mairie', $ds->date_echeance);
            }
            $ds->save($this->getUserId());
        }
        $this->ds = DSCivaClient::getInstance()->getDSPrincipaleByDs($ds);

        $this->redirect('ds_etape_redirect', $this->ds);
    }

    public function executeRedirectEtape(sfWebRequest $request) {
        $this->secureDS(array(DSSecurity::CONSULTATION,
                              DSSecurity::EDITION));

        $this->ds = $this->getRoute()->getDS();
        $this->tiers = $this->getRoute()->getTiers();
        $this->dss = DSCivaClient::getInstance()->findDssByDS($this->ds);
        if((!$this->ds) || (!$this->ds->exist('num_etape')))
         throw new sfException("La DS n'existe pas ou ne possède pas de numéro d'étape");
        $etape = $this->ds->num_etape;
        switch ($etape) {
         case 1:
             $this->redirect('ds_exploitation', $this->ds);
         break;
         case 2:
             $this->redirect("ds_lieux_stockage", $this->ds);
         break;
         case 3:
             $this->redirectEtapeAfterStock($this->ds);
         break;
         case 4:
             $this->redirect("ds_autre", $this->ds);
         break;
         case 5:
             $this->redirect("ds_validation", $this->ds);
         break;
         case 6:
             $this->redirect("ds_visualisation", $this->ds);
         break;
        }
        $id = $this->ds->_id;
        throw new sfException("Etape de DS $id non reconnu ($etape)");
    }

    public function executeExploitation(sfWebRequest $request)
    {
        $this->secureDS(array(DSSecurity::CONSULTATION,
                              DSSecurity::EDITION));

        $this->ds = $this->getRoute()->getDS();
        $this->tiers = $this->getRoute()->getEtablissement();
        $this->dss = DSCivaClient::getInstance()->findDssByDS($this->ds);
        $this->form_gest = null;
        $this->form_gest_err = 0;
        $this->form_gest = new TiersExploitantForm($this->ds->declarant->exploitant);
        $this->form_gest_err = 0;

        $this->form_expl = new TiersExploitationForm($this->ds->declarant);
        $this->form_expl_err = 0;
        if ($request->isMethod(sfWebRequest::POST)) {
            if($request->isXmlHttpRequest())
            {
                $this->ds->updateEtape(2);
                $this->ds->save($this->getUserId());
                return $this->renderText(json_encode(array("success" => true)));
            }
            if ($request->getParameter('gestionnaire')) {
                $this->form_gest->bind($request->getParameter($this->form_gest->getName()));
                if ($this->form_gest->isValid()) {
                    $this->form_gest->save();
                } else {
                    $this->form_gest_err = 1;
                }
            }
            if ($request->getParameter('exploitation')) {
                $this->form_expl->bind($request->getParameter($this->form_expl->getName()));
                if ($this->form_expl->isValid()) {
                    $tiers = $this->form_expl->save();
                } else {
                    $this->form_expl_err = 1;
                }
            }
            if (!$this->form_gest_err && !$this->form_expl_err) {
                $this->redirect('ds_exploitation', $this->ds);
            }
        }

        $suivant = isset($request['suivant']) && $request['suivant'];
        if($suivant){
            $this->ds->updateEtape(2);
            $this->ds->save($this->getUserId());
            $this->redirect("ds_lieux_stockage", $this->ds);
        }
    }

    public function executeLieuxStockage(sfWebRequest $request)
    {
        $this->secureDS(array(DSSecurity::CONSULTATION,
                              DSSecurity::EDITION));

        $this->ds = $this->getRoute()->getDS();
        $this->dss = DSCivaClient::getInstance()->findDssByDS($this->ds);
        $this->tiers = $this->getRoute()->getTiers();
        $this->form = new DSLieuxDeStockageForm($this->ds);
        $ds_neant = false;
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if($this->form->isValid()) {
               $this->dss = $this->form->doUpdateDss($this->dss);
               $this->dss_to_save = array();
                foreach ($this->dss as $current_ds) {
                    if($current_ds->isDsPrincipale() && $current_ds->hasNoAppellation()){
                        if($this->hasOneAppellationInDSS($this->dss)){
                            throw new sfException("Il n'est pas possible d'enregistrer un DS principale sans appellation");
                        }
                        if($current_ds->isDsNeant()){
                            $ds_neant = true;
                        }
                    }
                    if(!$current_ds->isDsPrincipale() && $current_ds->hasNoAppellation()){
                        if(DSCivaClient::getInstance()->find($current_ds->_id)){
                            DSCivaClient::getInstance()->delete($current_ds);
                        }
                    }else{
                        $this->dss_to_save[$current_ds->_id] = $current_ds;
                        }
                    }
                    $ds_principale = null;
                    foreach ($this->dss_to_save as $ds_to_save) {
                        if($ds_to_save->isDsPrincipale()){
                            if($ds_neant){
                                $ds_to_save->updateEtape(4);
                            }else{
                            $ds_to_save->updateEtape(3, $ds_to_save, $ds_to_save->getFirstAppellation()->getHash(),$ds_to_save);
                            }
                            $ds_principale = $ds_to_save;
                        }
                        $ds_to_save->save($this->getUserId());
                    }
                if($ds_neant){
                    $this->redirect('ds_autre', $ds_principale);
                }
                if($request->isXmlHttpRequest())
                {
                    return $this->renderText(json_encode(array("success" => true)));
                }
                $this->redirect('ds_edition_operateur', array('id' => DSCivaClient::getInstance()->getFirstDSByDs($this->ds)->_id));
            } else {
                $this->error = true;
            }
        }
    }

    public function executeLieuxStockageAjout(sfWebRequest $request)
    {
        $this->ds = $this->getRoute()->getDS();
        $this->tiers = $this->getRoute()->getTiers();
        if(!$this->ds->isAjoutLieuxDeStockage()){
            $this->forwardSecure();
        }

        $this->form = new DSEditionAddLieuStockageFormCiva($this->ds);

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->doAddLieuStockage();
                $this->redirect("ds_lieux_stockage", $this->ds);
            }
        }

    }

    public function executeStock(sfWebRequest $request) {
        $this->secureDS(array(DSSecurity::CONSULTATION,
                              DSSecurity::EDITION));

        $this->ds = $this->getRoute()->getDS();
        $this->tiers = $this->getRoute()->getTiers();
        if($this->ds->hasNoAppellation()){
            $this->redirect("ds_lieux_stockage", $this->ds);
        }
        if(!$this->getRoute()->getNoeud()) {

            return $this->redirect('ds_edition_operateur', $this->ds->getFirstAppellation());
        }

        if($this->getRoute()->getNoeud() instanceof DSAppellation) {

            if(count($this->getRoute()->getNoeud()->getLieux()) < 1 && count($this->getRoute()->getNoeud()->getConfig()->getChildrenNode()->getFirst()->getChildrenNode()) > 1) {

                return $this->redirect('ds_ajout_lieu', $this->getRoute()->getNoeud());
            }

            return $this->redirect('ds_edition_operateur', $this->getRoute()->getNoeud()->getLieux()->getFirst());
        }

        $this->lieu = $this->getRoute()->getNoeud();


        if(count($this->lieu->getProduitsDetails()) < 1) {

            return $this->redirect('ds_ajout_produit', $this->lieu);
        }

        $this->produit_key = $request->getParameter('produit', null);

        $this->form = new DSEditionFormCiva($this->ds, $this->lieu);

        $this->appellations = $this->ds->declaration->getAppellationsSorted();
        if($this->getRoute()->getNoeud() instanceof DSGenre) {
            $this->appellation = $this->lieu;
        } else {
            $this->appellation = $this->lieu->getAppellation();
        }
        $this->current_lieu = null;
        $this->isFirstAppellation = ($this->ds->getFirstAppellation()->getHash() == $this->appellation->getHash());

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->doUpdateObject();
                $this->ds->updateEtape(3);
                $this->ds->save($this->getUserId());
                if($request->isXmlHttpRequest())
                {
                    return $this->renderText(json_encode(array("success" => true, "document" => array("id" => $this->ds->get('_id'),"revision" => $this->ds->get('_rev')))));
                }

                $next = $this->ds->getNextLieu($this->lieu);
                $next_hash = (!$next)? $this->lieu->getHash() : $next->getHash();//$this->convertNodeForUrl($this->lieu) : $this->convertNodeForUrl($next);
                $this->ds->updateEtape(3,$this->ds, $next_hash);
                $this->ds->save($this->getUserId());
                if($next){
                    $this->redirect('ds_edition_operateur', $next);
                }
                else
                {
                    $this->redirect('ds_recapitulatif_lieu_stockage', array('id' => $this->ds->_id));
                }
            }
        }
    }

    public function executeAjoutAppellation(sfWebRequest $request) {
        $this->secureDS(array(DSSecurity::CONSULTATION,
                              DSSecurity::EDITION));

        $this->ds = $this->getRoute()->getDS();
        $this->tiers = $this->getRoute()->getTiers();
        $this->form = new DSEditionAddAppellationFormCiva($this->ds);

        if (!$request->isMethod(sfWebRequest::POST)) {

            return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()));

        if(!$this->form->isValid()) {

            return sfView::SUCCESS;
        }

        $lieu = $this->ds->addAppellation($this->form->getValue('hashref'));
        $this->ds->save($this->getUserId());

        return $this->redirect('ds_edition_operateur', $lieu);
    }

    public function executeAjoutLieu(sfWebRequest $request) {
        $this->secureDS(array(DSSecurity::CONSULTATION,
                              DSSecurity::EDITION));

        $this->ds = $this->getRoute()->getDS();
        $this->tiers = $this->getRoute()->getTiers();
        $this->appellation = $this->getRoute()->getNoeud();

        $this->config_appellation = $this->appellation->getConfig();
        $this->form = new DSEditionAddLieuFormCiva($this->ds, $this->config_appellation);

        if (!$request->isMethod(sfWebRequest::POST)) {

            return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()));

        if(!$this->form->isValid()) {

            return sfView::SUCCESS;
        }

        $lieu = $this->ds->addLieu($this->form->getValue('hashref'));
        $this->ds->save($this->getUserId());

        return $this->redirect('ds_edition_operateur', $lieu);
    }

    public function executeAjoutProduit(sfWebRequest $request) {
        $this->secureDS(array(DSSecurity::CONSULTATION,
                              DSSecurity::EDITION));

        $this->ds = $this->getRoute()->getDS();
        $this->tiers = $this->getRoute()->getTiers();
        $this->lieu = $this->getRoute()->getNoeud();
        $this->config_lieu = $this->lieu->getConfig();
        $this->appellation = $this->lieu->getAppellation();
        $this->form = new DSEditionAddProduitFormCiva($this->ds, $this->config_lieu);

        if (!$request->isMethod(sfWebRequest::POST)) {

            return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()));

        if(!$this->form->isValid()) {

            return sfView::SUCCESS;
        }

        $detail = $this->ds->addDetail($this->form->getValue('hashref'), $this->form->getValue('lieudit'));
        $this->ds->save($this->getUserId());

        return $this->redirect('ds_edition_operateur', array('sf_subject' => $this->lieu, 'produit' => $detail->getHashForKey()));
    }

    public function executeRecapitulatifLieuStockage(sfWebRequest $request) {
        $this->secureDS(array(DSSecurity::CONSULTATION,
                              DSSecurity::EDITION));

        $this->ds = $this->getRoute()->getDS();
        $this->ds_principale = DSCivaClient::getInstance()->getDSPrincipaleByDs($this->ds);
        $this->tiers = $this->getRoute()->getTiers();
        $suivant = isset($request['suivant']) && $request['suivant'];
        if($suivant){
            $nextDs = DSCivaClient::getInstance()->getNextDS($this->ds);
            if($nextDs){
                $this->ds_principale->updateEtape(3, $nextDs);
                $this->ds_principale->save($this->getUserId());
                $this->redirect('ds_edition_operateur', array('id' => $nextDs->_id,'appellation_lieu' => $nextDs->getFirstAppellation()));
            }
            else{
                $this->ds_principale->updateEtape(4);
                $this->ds_principale->save($this->getUserId());
                $this->redirect('ds_autre', $this->ds_principale);
            }
        }
    }

    public function executeAutre(sfWebRequest $request)
    {
        $this->secureDS(array(DSSecurity::CONSULTATION,
                              DSSecurity::EDITION));

        $this->ds = $this->getRoute()->getDS();
        $this->tiers = $this->getRoute()->getTiers();
        $this->form = new DSEditionCivaAutreForm($this->ds);
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->ds->updateEtape(5);
                $this->form->save($this->getUserId());
                if($request->isXmlHttpRequest())
                {
                    return $this->renderText(json_encode(array("success" => true, "document" => array("id" => $this->ds->get('_id'),"revision" => $this->ds->get('_rev')))));
                }
                $this->redirect('ds_validation', $this->ds);
            }
        }
    }

    public function executeValidation(sfWebRequest $request)
    {
        $this->secureDS(array(DSSecurity::CONSULTATION,
                              DSSecurity::EDITION));

        $this->isAdmin = $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN);
        $this->ds_principale = $this->getRoute()->getDS();
        $this->tiers = $this->getRoute()->getTiers();
        $this->ds_client = DSCivaClient::getInstance();
        $this->dss = $this->ds_client->findDssByDS($this->ds_principale);
        $this->validation_dss = array();
        $this->formDatesModification = null;
        if($this->isAdmin){
            $this->formDatesModification = new DSEditionDatesModificationFormCiva($this->ds_principale,$this->getUser());
        }

        foreach ($this->dss as $id_ds => $ds) {
            $this->validation_dss[$id_ds] = new DSValidationCiva($ds);
        }
        if ($request->isMethod(sfWebRequest::POST)) {
            foreach ($this->dss as $id_ds => $ds) {
                if(!$this->validation_dss[$id_ds]->isValide())
                    throw new sfException("Il existe un point bloquant non résolue, il n'est pas possible de valider la DS $id_ds");
                }
                DSCivaClient::getInstance()->validate($this->ds_principale,$this->getUser()->getCompte(CompteSecurityUser::NAMESPACE_COMPTE_AUTHENTICATED)->get('_id'));

                $mess = 'Bonjour ' . $this->tiers->nom . ',

Vous venez de valider votre déclaration de Stocks pour l\'année ' . date("Y") . '. Pour la visualiser rendez-vous sur votre espace civa : ' . sfConfig::get('app_base_url') . '/mon_espace_civa

Cordialement,

Le CIVA';

                //send email

                $message = $this->getMailer()->compose(array(sfConfig::get('app_email_from') => sfConfig::get('app_email_from_name')),
                                                       $this->getUser()->getCompte()->email,
                                                       'CIVA - Validation de votre déclaration de Stocks', $mess);
                if (!$this->getUser()->hasCredential(CompteSecurityUser::CREDENTIAL_OPERATEUR)) {
                    try {
                        $this->getMailer()->send($message);
                    } catch (Exception $e) {
                        $this->getUser()->setFlash('error', 'Erreur de configuration : Mail de confirmation non envoyé, veuillez contacter CIVA');
                    }
                }
                if($this->isAdmin){
                   $ds = DSCivaClient::getInstance()->getDSPrincipaleByDs($this->ds_principale);
                   $this->formDatesModification->doUpdateDatesModificationValidation($request->getParameter("ds_edit_dates"),$ds);
                }
                $this->redirect('ds_confirmation', $this->ds_principale);
        }
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeConfirmation(sfWebRequest $request) {
        $this->secureDS(array(DSSecurity::CONSULTATION));

        $this->ds_principale = $this->getRoute()->getDS();
        $this->tiers = $this->getRoute()->getTiers();
    }

    public function executeInvaliderCiva(sfWebRequest $request) {
        if(!$this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN)) {

            return $this->forwardSecure();
        }

        $this->ds_principale = $this->getRoute()->getDS();

        DSCivaClient::getInstance()->devalidate($this->ds_principale, true);

        $this->redirect('mon_espace_civa_ds', array("type" => $this->ds_principale->type_ds, 'sf_subject' => $this->ds_principale->getEtablissement()));
    }

    public function executeInvaliderRecoltant(sfWebRequest $request) {
        if(!$this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN)) {
            return $this->forwardSecure();
        }

        $this->ds_principale = $this->getRoute()->getDS();

        DSCivaClient::getInstance()->devalidate($this->ds_principale);

        $this->redirect('mon_espace_civa_ds', array("type" => $this->ds_principale->type_ds, 'sf_subject' => $this->ds_principale->getEtablissement()));
    }


    public function executeVisualisation(sfWebRequest $request)
    {
        $this->secureDS(array(DSSecurity::CONSULTATION));

        $this->ds_principale = $this->getRoute()->getDS();

        $this->tiers = $this->getRoute()->getTiers();
        $this->ds_client = DSCivaClient::getInstance();
    }


    public function executeSendEmail(sfWebRequest $request){
        $this->secureDS(array(DSSecurity::CONSULTATION));

        $this->ds = $this->getRoute()->getDS();

        $this->tiers = $this->getRoute()->getTiers();

        $document = new ExportDSPdf($this->ds, array($this, 'getPartial'), true, $this->getRequestParameter('output', 'pdf'));
        $document->generatePDF();

        $pdfContent = $document->output();

        // si l'on vient de la page de visualisation
        if($request->getParameter('message', null) != "custom")
        {
            $mess = 'Bonjour ' . $this->tiers->nom . ',

Vous trouverez ci-joint votre Déclaration de Stocks pour l\'année ' . $this->ds->getAnnee() . '.

Cordialement,

Le CIVA';

        }else{

            $mess = 'Bonjour ' . $this->tiers->nom . ',

    Vous trouverez ci-joint votre Déclaration de Stocks pour l\'année ' . $this->ds->getAnnee() . ' que vous venez de valider.

    Cordialement,

    Le CIVA';

        }

        //send email
        $message = Swift_Message::newInstance()
                ->setFrom(array(sfConfig::get('app_email_from') => sfConfig::get('app_email_from_name')))
                ->setTo($this->getUser()->getCompte()->email)
                ->setSubject('CIVA - Votre déclaration de Stocks')
                ->setBody($mess);


        $attachment = new Swift_Attachment($pdfContent, $document->getFileName(), 'application/pdf');
        $message->attach($attachment);

        try {
            $this->getMailer()->send($message);
        } catch (Exception $e) {

            $this->emailSend = false;
        }

        $this->emailSend = true;
    }


    protected function redirectEtapeAfterStock($ds){
            $courant_stock = ($ds->exist('courant_stock'))? $ds->courant_stock : null;

            if(!$courant_stock && !$ds->isTypeDsNegoce()){
                return $this->redirect('ds_edition_operateur', $ds);
            }

            $courant_id = preg_replace('/^(DS-[0-9]{10}-[0-9]{6}-[0-9]{3})-([A-Za-z0-9\_\-\/]*)/', '$1', $courant_stock);

            $ds_courante = DSCivaClient::getInstance()->find($courant_id);

            if(!$ds_courante) {
                return $this->redirect('ds_edition_operateur', $ds);
            }

            $hash_lieu = preg_replace('/^(DS-[0-9]{10}-[0-9]{6}-[0-9]{3})-([A-Za-z0-9\_\-\/]*)/', '$2', $courant_stock);
            $node = null;
            if($hash_lieu) {
                $node = DSCivaClient::getInstance()->find($courant_id)->exist($hash_lieu);
            }

            if(!$node){
                $this->redirect('ds_edition_operateur', $ds_courante);
            }

            $this->redirect('ds_edition_operateur', array('sf_subject' => $ds_courante, 'hash' => $this->convertNodeForUrl($node)));

    }

    protected function convertNodeForUrl($node) {
        if($node instanceof sfOutputEscaperIteratorDecorator) {
            $node = $node->getRawValue();
        }

        $hash = null;

        if($node instanceof DSAppellation) {
            $hash = str_replace("appellation_" , "", $node->getKey());
        }

        if($node instanceof DSLieu) {
            $hash = preg_replace('/-$/', '', sprintf("%s-%s", str_replace("appellation_" , "", $node->getAppellation()->getKey()), str_replace("lieu" , "", $node->getKey())));
        }
        return $hash;
    }

    protected function hasOneAppellationInDSS($dss){
        foreach ($dss as $ds) {
            if(!$ds->hasNoAppellation()) return true;
        }
        return false;
    }

    protected function secureDS($droits, $ds = null, $type_ds = null) {
        if(is_null($ds) && $this->getRoute() instanceof DSRoute) {
            $ds = $this->getRoute()->getDS();
        }

        if(!DSSecurity::getInstance($this->getRoute()->getEtablissement(), $ds, $type_ds)->isAuthorized($droits)) {

            return $this->forwardSecure();
        }
    }

    protected function forwardSecure()
    {
        $this->context->getController()->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));

        throw new sfStopException();
    }

    public function executeMessageAjax(sfWebRequest $request) {
        $this->forward404Unless($request->isXmlHttpRequest());
        return $this->renderText(json_encode(array('titre' => $request->getParameter('title', null),
						   'url_doc' => $request->getParameter('url_doc', $this->generateUrl('telecharger_la_notice_ds', array("type" => DSCivaClient::TYPE_DS_PROPRIETE))),
                                                   'message' => acCouchdbManager::getClient('Messages')->getMessage($request->getParameter('id', null)))));

    }

    public function executeDownloadNotice(sfWebRequest $request) {
        $type_ds = $request->getParameter('type');

        if($type_ds == DSCivaClient::TYPE_DS_NEGOCE) {

            return $this->renderPdf(sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . "helpPdf/aide_stock_negoce.pdf", "aide stock negoce.pdf");
        }

        if($type_ds == DSCivaClient::TYPE_DS_PROPRIETE) {

            return $this->renderPdf(sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . "helpPdf/aide_stock_propriete.pdf", "aide stock propriete.pdf");

        }

        return $this->forward404();
    }

    public function executeDownloadDai() {
        return $this->renderPdf(sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . "helpPdf/dai.pdf", "declaration annuelle inventaire.pdf");
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

    private function getUserId() {
        return $this->getUser()->getCompte(CompteSecurityUser::NAMESPACE_COMPTE_AUTHENTICATED)->get('_id');
    }

    public function executeFeedBack(sfWebRequest $request) {
        $this->type_ds = $request->getParameter("type");
        $this->identifiant = $this->getUser()->getDeclarantDS($this->type_ds)->identifiant;

        $this->form = new FeedBackForm();

        if(!$request->isMethod(sfWebRequest::POST)) {

            return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()));

        if (!$this->form->isValid()) {

            return sfView::SUCCESS;
        }

        $message = $this->form->getValue('message');

        /*$mess = 'Bonjour ' . $this->tiers->nom . ',

Vous trouverez ci-joint votre Déclaration de Stocks pour l\'année ' . $this->ds->getAnnee() . ' que vous venez de valider.

Cordialement,

Le CIVA';*/

        $message .= "\n\n-------\n\n".$this->getUser()->getCompte()->nom."\nCVI/CIVABA: ".$this->getUser()->getDeclarantDS($this->type_ds)->identifiant;

        $to = sfConfig::get('app_email_feed_back');
        $this->emailSend = true;
        $mail = Swift_Message::newInstance()
                ->setFrom($this->getUser()->getCompte()->email)
                ->setTo($to)
                ->setSubject("CIVA - Retour d'expérience sur la Déclaration de Stocks")
                ->setBody($message);

        try {
            if(is_array($to) && count($to) < 1) {
                throw new sfException("emails not configure in app.yml email->feed_back");
            }

            $this->getMailer()->send($mail);
        } catch (Exception $e) {

            $this->emailSend = false;
        }

        return $this->redirect('ds_feed_back_confirmation', array("type" => $this->type_ds));
    }

    public function executeFeedBackConfirmation(sfWebRequest $request) {
        $this->type_ds = $request->getParameter("type");
        $this->identifiant = $this->getUser()->getDeclarantDS($this->type_ds)->identifiant;
    }
}
