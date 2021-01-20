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

    public function demandeSignature($vrac, $destinataire)
    {
        $from = self::getFrom();
        $to = array($destinataire);
        $proprietaire = $vrac->getCreateurInformations();
        $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
        $subject = '[Contrat '.strtolower($vrac->type_contrat).'] Demande de signature ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
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
        $subject = '[Contrat '.strtolower($vrac->type_contrat).'] Confirmation de signature ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
        $body = self::getBodyFromPartial('vrac_confirmation_signature', array('vrac' => $vrac));
        $message = self::getMailer()->compose($from, $to, $subject, $body);

        return self::getMailer()->send($message);
    }

    public function validationContrat($vrac, $destinataire, $document)
    {
		if($vrac->isPapier()) {

			return $this->validationContratPapier($vrac, $destinataire);
		}
        $from = self::getFrom();
        $to = array($destinataire);
        $proprietaire = $vrac->getCreateurInformations();
        $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
        $subject = '[Contrat '.strtolower($vrac->type_contrat).'] Validation du contrat n° '.$vrac->numero_visa.' ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
        $body = self::getBodyFromPartial('vrac_validation_contrat', array('vrac' => $vrac));
		$message = Swift_Message::newInstance()
  					->setFrom($from)
  					->setTo($to)
  					->setSubject($subject)
  					->setBody($body)
  					->attach(new Swift_Attachment($document->output(), $document->getFileName(), 'application/pdf'));

        return self::getMailer()->send($message);
    }

	public function validationContratPapier($vrac, $destinataire)
    {
        $from = self::getFrom();
        $to = array($destinataire);
        $body = self::getBodyFromPartial('vrac_validation_contrat_papier', array('vrac' => $vrac));
	    $subject = '[Contrat '.strtolower($vrac->type_contrat).'] Validation de votre contrat papier n° '.$vrac->numero_papier;
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
				$cc = array(sfConfig::get('app_email_reply_to') => sfConfig::get('app_email_from_name'));
        $proprietaire = $vrac->getCreateurInformations();
        $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
        $subject = '[Contrat '.strtolower($vrac->type_contrat).'] Annulation ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
        $body = self::getBodyFromPartial('vrac_annulation_contrat', array('vrac' => $vrac));
				$message = Swift_Message::newInstance()
		  					->setFrom($from)
		  					->setTo($to)
		  					->setCc($cc)
		  					->setSubject($subject)
		  					->setBody($body);
        return self::getMailer()->send($message);
    }

    public function clotureContrat($vrac, $destinataire, $document)
    {
        $from = self::getFrom();
        $to = array($destinataire);
        $proprietaire = $vrac->getCreateurInformations();
        $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
        $subject = '[Contrat '.strtolower($vrac->type_contrat).'] Cloture du contrat n° '.$vrac->numero_visa.' ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
        $body = self::getBodyFromPartial('vrac_cloture_contrat_'.strtolower($vrac->type_contrat), array('vrac' => $vrac));
		$message = Swift_Message::newInstance()
  					->setFrom($from)
  					->setTo($to)
  					->setSubject($subject)
  					->setBody($body)
  					->attach(new Swift_Attachment($document->output(), $document->getFileName(), 'application/pdf'));

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
