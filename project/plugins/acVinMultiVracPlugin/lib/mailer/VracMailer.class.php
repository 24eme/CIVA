<?php

class VracMailer {

	private static $_instance = null;

	public function __construct() { }

	public static function getInstance()
    {
       	if(is_null(self::$_instance)) {
       		self::$_instance = new VracMailer();
		}
		return self::$_instance;
    }

    public function getPrefixSubject($vrac) {

        return '['.$vrac->getTypeDocumentLibelle().' '.strtolower($vrac->getTypeDureeLibelle()).' '.strtolower($vrac->type_contrat).']';
    }

    public function sendMailsByStatutsChanged($vrac) {
        $messages = [];
        foreach($vrac->getStatutsChanged() as $statut => $auteur) {
            $messages = array_merge($messages, $this->getMessagesByStatut($vrac, $statut, $auteur));
        }

        foreach($messages as $message) {
            $this->getMailer()->send($message);
        }

        if($vrac->isValide() && !$vrac->valide->email_validation) {
            $vrac->valide->email_validation = date('Y-m-d');
            $vrac->save();
        }

        if($vrac->isCloture() && !$vrac->valide->email_cloture) {
            $vrac->valide->email_cloture = date('Y-m-d');
            $vrac->save();
        }
    }

	public function getMessagesByStatut($vrac, $statut, $auteur, $pdf = true) {
        if($statut == Vrac::STATUT_PROJET_VENDEUR_TRANSMIS) {

            return $this->demandeValidationAcheteurCourtier($vrac);
        }

        if($statut == Vrac::STATUT_PROJET_ACHETEUR) {

            return $this->demandeSignatureVendeur($vrac);
        }

        if($statut == Vrac::STATUT_REFUS_PROJET) {

            return $this->refusProjet($vrac);
        }

        if($statut == Vrac::STATUT_PROPOSITION) {

            return $this->demandeSignature($vrac);
        }

        if($statut == Vrac::STATUT_PROPOSITION) {

            return $this->demandeSignature($vrac);
        }

        if($statut == Vrac::STATUT_SIGNE) {

            return $this->confirmationSignature($vrac, $auteur);
        }

        if($statut == Vrac::STATUT_VALIDE) {

            return $this->validationContrat($vrac, $pdf);
        }

        if($statut == Vrac::STATUT_CLOTURE) {

            return $this->clotureContrat($vrac, $pdf);
        }

        if($statut == Vrac::STATUT_ANNULE) {

            return $this->annulationContrat($vrac, $pdf);
        }

		return [];
	}

        public function refusProjet($vrac)
    {
        $acteurs = [$vrac->createur_identifiant];

        $messages = [];
        foreach($acteurs as $acteur_id) {
            $from = self::getFrom();
            $to = $vrac->getEmailsActeur($acteur_id);
            $proprietaire = $vrac->getCreateurInformations();
            $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
            $subject = $this->getPrefixSubject($vrac).' Refus du projet ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
            $body = self::getBodyFromPartial('vrac_refus_projet', array('vrac' => $vrac));
    		$message = Swift_Message::newInstance()
      					->setFrom($from)
      					->setTo($to)
      					->setSubject($subject)
      					->setBody($body);
            $messages[] = $message;
        }

        if (count($messages)) {
            $message = clone $messages[0];
            $message->setTo(sfConfig::get('app_email_notifications', array()));
            $messages[] = $message;
        }

        return $messages;
    }

    public function refusApplication($vrac)
    {
        $acteurs = [$vrac->vendeur_identifiant, $vrac->acheteur_identifiant];
        if($vrac->hasCourtier()) {
            $acteurs[] = $vrac->mandataire_identifiant;
        }

        $messages = array();
        foreach($acteurs as $acteur_id) {
            $from = self::getFrom();
            $to = $vrac->getEmailsActeur($acteur_id);
            $proprietaire = $vrac->getCreateurInformations();
            $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
            $subject = $this->getPrefixSubject($vrac).' Refus du vendeur ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
            $body = self::getBodyFromPartial('vrac_refus_application', array('vrac' => $vrac));
    		$message = Swift_Message::newInstance()
      					->setFrom($from)
      					->setTo($to)
      					->setSubject($subject)
      					->setBody($body);
            $messages[] = $message;
        }

        return $messages;
    }

    public function demandeValidationAcheteurCourtier($vrac)
    {
        $from = self::getFrom();

        $to = $vrac->getEmailsActeur($vrac->createur_identifiant);
        $subject = $this->getPrefixSubject($vrac).' Demande de validation ('.trim($vrac->vendeur->intitule.' '.$vrac->vendeur->raison_sociale).' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
        $body = self::getBodyFromPartial('vrac_demande_validation_acheteur_courtier', array('vrac' => $vrac));
        $message = self::getMailer()->compose($from, $to, $subject, $body);

        return [$message];
    }

    public function demandeSignatureVendeur($vrac)
    {
        $from = self::getFrom();
        $to = $vrac->getEmailsActeur($vrac->vendeur_identifiant);
        $proprietaire = $vrac->getCreateurInformations();
        $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
        $subject = $this->getPrefixSubject($vrac).' Demande de signature ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
        $body = self::getBodyFromPartial('vrac_demande_signature_vendeur', array('vrac' => $vrac));
        $message = self::getMailer()->compose($from, $to, $subject, $body);

        return [$message];
    }

    public function demandeSignature($vrac)
    {
        $acteurs = [$vrac->acheteur_identifiant];
        if($vrac->hasCourtier()) {
            $acteurs[] = $vrac->mandataire_identifiant;
        }
        if($vrac->isApplicationPluriannuel()) {
            $acteurs = [$vrac->vendeur_identifiant];
        }
        if($vrac->isApplicationPluriannuel() && $vrac->hasCourtier()) {
            $acteurs[] = $vrac->acheteur_identifiant;
        }
        $messages = array();
        foreach($acteurs as $acteur_id) {
            $from = self::getFrom();
            $to = $vrac->getEmailsActeur($acteur_id);
            $proprietaire = $vrac->getCreateurInformations();
            $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
            $subject = $this->getPrefixSubject($vrac).' Demande de signature ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
            $body = self::getBodyFromPartial('vrac_demande_signature', array('vrac' => $vrac));
            $messages[] = self::getMailer()->compose($from, $to, $subject, $body);
        }

        return $messages;
    }

    public function confirmationSignature($vrac, $acteur_id)
    {
        $from = self::getFrom();
        $to = $vrac->getEmailsActeur($acteur_id);
        $proprietaire = $vrac->getCreateurInformations();
        $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
        $subject = $this->getPrefixSubject($vrac).' Confirmation de signature ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
        $body = self::getBodyFromPartial('vrac_confirmation_signature', array('vrac' => $vrac));
        $message = self::getMailer()->compose($from, $to, $subject, $body);

        return [$message];
    }

    public function validationContrat($vrac, $pdf = true)
    {
		if($vrac->isPapier()) {

			return $this->validationContratPapier($vrac);
		}

        if($pdf) {
            $pdf = new ExportVracPdf($vrac, false, array(sfContext::getInstance()->getController()->getAction('vrac_export', 'main'), 'getPartial'));
            $pdf->generatePDF();
        }
        $acteurs = [$vrac->vendeur_identifiant, $vrac->acheteur_identifiant];
        if($vrac->hasCourtier()) {
            $acteurs[] = $vrac->mandataire_identifiant;
        }

        $messages = array();
        foreach($acteurs as $acteur_id) {
            $from = self::getFrom();
            $to = $vrac->getEmailsActeur($acteur_id);
            $proprietaire = $vrac->getCreateurInformations();
            $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
            $subject = $this->getPrefixSubject($vrac).' Validation du contrat n° '.$vrac->numero_visa.' ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
            if($vrac->isApplicationPluriannuel()) {
                $subject = $this->getPrefixSubject($vrac)." Validation du contrat d'application ".$vrac->campagne." n° ".$vrac->numero_visa.' ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
            }
            $body = self::getBodyFromPartial('vrac_validation_contrat', array('vrac' => $vrac));
    		$message = Swift_Message::newInstance()
      					->setFrom($from)
      					->setTo($to)
      					->setSubject($subject)
      					->setBody($body);

            if($pdf) {
      			$message->attach(new Swift_Attachment($pdf->output(), $pdf->getFileName(), 'application/pdf'));
            }

            $messages[] = $message;
        }
        if ($vrac->declaration->hashProduitsWithVolumeBloque() && count($messages)) {
            $message = clone $messages[0];
            $message->setTo(sfConfig::get('app_email_notifications', array()));
            $messages[] = $message;
        }

        return $messages;
    }

	public function validationContratPapier($vrac)
    {
        $acteurs = [$vrac->vendeur_identifiant, $vrac->acheteur_identifiant];
        if($vrac->hasCourtier()) {
            $acteurs[] = $vrac->mandataire_identifiant;
        }

        $messages = array();
        foreach($acteurs as $acteur_id) {
            $from = self::getFrom();
            $to = $vrac->getEmailsActeur($acteur_id);
            $body = self::getBodyFromPartial('vrac_validation_contrat_papier', array('vrac' => $vrac));
    	    $subject = $this->getPrefixSubject($vrac).' Validation de votre contrat papier n° '.$vrac->numero_papier;
    		$message = Swift_Message::newInstance()
      					->setFrom($from)
      					->setTo($to)
      					->setSubject($subject)
      					->setBody($body);
            $messages[] = $message;
        }

        return $messages;
    }

    public function annulationContrat($vrac)
    {
        $acteurs = [$vrac->vendeur_identifiant, $vrac->acheteur_identifiant];
        if($vrac->hasCourtier()) {
            $acteurs[] = $vrac->mandataire_identifiant;
        }

        $messages = array();
        foreach($acteurs as $acteur_id) {
            $from = self::getFrom();
            $to = $vrac->getEmailsActeur($acteur_id);
            $proprietaire = $vrac->getCreateurInformations();
            $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
            $subject = $this->getPrefixSubject($vrac).' Annulation ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
            $body = self::getBodyFromPartial('vrac_annulation_contrat', array('vrac' => $vrac));
    		$message = Swift_Message::newInstance()
      					->setFrom($from)
      					->setTo($to)
      					->setSubject($subject)
      					->setBody($body);
            $messages[] = $message;
        }

        if (count($messages)) {
            $message = clone $messages[0];
            $message->setTo(sfConfig::get('app_email_notifications', array()));
            $messages[] = $message;
        }

        return $messages;
    }

    public function clotureContrat($vrac, $pdf = true)
    {
        if($pdf) {
            $pdf = new ExportVracPdf($vrac, false, array(sfContext::getInstance()->getController()->getAction('vrac_export', 'main'), 'getPartial'));
            $pdf->generatePDF();
        }
        $acteurs = [$vrac->vendeur_identifiant, $vrac->acheteur_identifiant];
        if($vrac->hasCourtier()) {
            $acteurs[] = $vrac->mandataire_identifiant;
        }

        $messages = array();
        foreach($acteurs as $acteur_id) {
            $from = self::getFrom();
            $to = $vrac->getEmailsActeur($acteur_id);
            $proprietaire = $vrac->getCreateurInformations();
            $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
            $subject = $this->getPrefixSubject($vrac).' Clôture du contrat n° '.$vrac->numero_visa.' ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
            if($vrac->isApplicationPluriannuel()) {
                $subject = $this->getPrefixSubject($vrac)." Clôture du contrat d'application ".$vrac->campagne." n° ".$vrac->numero_visa.' ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
            }
            $body = self::getBodyFromPartial('vrac_cloture_contrat', array('vrac' => $vrac));
    		$message = Swift_Message::newInstance()
      					->setFrom($from)
      					->setTo($to)
      					->setSubject($subject)
      					->setBody($body);

            if($pdf) {
      			$message->attach(new Swift_Attachment($pdf->output(), $pdf->getFileName(), 'application/pdf'));
            }

            $messages[] = $message;
        }

        return $messages;
    }

    protected static function getFrom()
    {
    	return array("ne_pas_repondre_contrat@vinsalsace.pro" => "Contrats CIVA");
    }

    protected static function getMailer()
    {
        return sfContext::getInstance()->getMailer();
    }

    protected static function getBodyFromPartial($partial, $vars = null)
    {
        return htmlspecialchars_decode(sfContext::getInstance()->getController()->getAction('email', 'main')->getPartial('email/' . $partial, $vars), ENT_QUOTES);
    }

}
