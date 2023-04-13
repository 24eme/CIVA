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

    public function refusProjet($vrac, $destinataire)
    {
        $from = self::getFrom();
        $to = array($destinataire);
        $proprietaire = $vrac->getCreateurInformations();
        $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
        $subject = $this->getPrefixSubject($vrac).' Refus du vendeur ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
        $body = self::getBodyFromPartial('vrac_refus_projet', array('vrac' => $vrac));
        $message = self::getMailer()->compose($from, $to, $subject, $body);

        return self::getMailer()->send($message);
    }

    public function demandeValidationAcheteurCourtier($vrac)
    {
        $from = self::getFrom();

        $emails = $vrac->getEmailsActeur($vrac->createur_identifiant);
        if($emails instanceof sfOutputEscaperArrayDecorator) {
            $emails = $emails->getRawValue();
        }

        $to = $vrac->getEmailsActeur($vrac->createur_identifiant)->getRawValue();
        $subject = $this->getPrefixSubject($vrac).' Demande de validation ('.trim($vrac->vendeur->intitule.' '.$vrac->vendeur->raison_sociale).' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
        $body = self::getBodyFromPartial('vrac_demande_validation_acheteur_courtier', array('vrac' => $vrac));
        $message = self::getMailer()->compose($from, $to, $subject, $body);

        return $message;
    }

    public function demandeSignatureVendeur($vrac)
    {
        $from = self::getFrom();
        $emails = $vrac->getEmailsActeur($vrac->vendeur_identifiant);
        if($emails instanceof sfOutputEscaperArrayDecorator) {
            $emails = $emails->getRawValue();
        }
        $to = $emails;
        $proprietaire = $vrac->getCreateurInformations();
        $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
        $subject = $this->getPrefixSubject($vrac).' Demande de signature ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
        $body = self::getBodyFromPartial('vrac_demande_signature_vendeur', array('vrac' => $vrac));
        $message = self::getMailer()->compose($from, $to, $subject, $body);

        return $message;
    }

    public function demandeSignature($vrac, $destinataire)
    {
        $from = self::getFrom();
        $to = array($destinataire);
        $proprietaire = $vrac->getCreateurInformations();
        $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
        $subject = $this->getPrefixSubject($vrac).' Demande de signature ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
        $body = self::getBodyFromPartial('vrac_demande_signature', array('vrac' => $vrac));
        $message = self::getMailer()->compose($from, $to, $subject, $body);

        return self::getMailer()->send($message);
    }

    public function confirmationSignature($vrac, $destinataire)
    {
        $from = self::getFrom();
        $to = array($destinataire);
        $proprietaire = $vrac->getCreateurInformations();
        $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
        $subject = $this->getPrefixSubject($vrac).' Confirmation de signature ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
        $body = self::getBodyFromPartial('vrac_confirmation_signature', array('vrac' => $vrac));
        $message = self::getMailer()->compose($from, $to, $subject, $body);

        return self::getMailer()->send($message);
    }

    public function validationContrat($vrac, $destinataire, $document, $bcc = null)
    {
		if($vrac->isPapier()) {

			return $this->validationContratPapier($vrac, $destinataire);
		}
        $from = self::getFrom();
        $to = array($destinataire);
        $proprietaire = $vrac->getCreateurInformations();
        $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
        $subject = $this->getPrefixSubject($vrac).' Validation du contrat n° '.$vrac->numero_visa.' ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
        $body = self::getBodyFromPartial('vrac_validation_contrat', array('vrac' => $vrac));
		$message = Swift_Message::newInstance()
  					->setFrom($from)
  					->setTo($to)
  					->setSubject($subject)
  					->setBody($body)
  					->attach(new Swift_Attachment($document->output(), $document->getFileName(), 'application/pdf'));
        if ($bcc) {
            $message->setBcc($bcc);
        }

        return self::getMailer()->send($message);
    }

	public function validationContratPapier($vrac, $destinataire)
    {
        $from = self::getFrom();
        $to = array($destinataire);
        $body = self::getBodyFromPartial('vrac_validation_contrat_papier', array('vrac' => $vrac));
	    $subject = $this->getPrefixSubject($vrac).' Validation de votre contrat papier n° '.$vrac->numero_papier;
		$message = Swift_Message::newInstance()
  					->setFrom($from)
  					->setTo($to)
  					->setSubject($subject)
  					->setBody($body);

        return self::getMailer()->send($message);
    }

    public function annulationContrat($vrac, $destinataire)
    {
        $from = self::getFrom();
        $to = array($destinataire);
        $proprietaire = $vrac->getCreateurInformations();
        $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
        $subject = $this->getPrefixSubject($vrac).' Annulation ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
        $body = self::getBodyFromPartial('vrac_annulation_contrat', array('vrac' => $vrac));
		$message = Swift_Message::newInstance()
  					->setFrom($from)
  					->setTo($to)
  					->setSubject($subject)
  					->setBody($body);
        return self::getMailer()->send($message);
    }

    public function clotureContrat($vrac, $destinataire, $document, $bcc = null)
    {
        $from = self::getFrom();
        $to = array($destinataire);
        $proprietaire = $vrac->getCreateurInformations();
        $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
        $subject = $this->getPrefixSubject($vrac).' Clôture du contrat n° '.$vrac->numero_visa.' ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
        $body = self::getBodyFromPartial('vrac_cloture_contrat_'.strtolower($vrac->type_contrat), array('vrac' => $vrac));
		$message = Swift_Message::newInstance()
  					->setFrom($from)
  					->setTo($to)
  					->setSubject($subject)
  					->setBody($body)
  					->attach(new Swift_Attachment($document->output(), $document->getFileName(), 'application/pdf'));
        if ($bcc) {
            $message->setBcc($bcc);
        }

        return self::getMailer()->send($message);
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
