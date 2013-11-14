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
        $to = array('jblemetayer@actualys.com');
        $proprietaire = $vrac->getCreateurInformations();
        $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
        $subject = '[Contrat vrac] Demande de signature ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
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
        $subject = '[Contrat vrac] Confirmation de signature ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
        $body = self::getBodyFromPartial('vrac_confirmation_signature', array('vrac' => $vrac));
        $message = self::getMailer()->compose($from, $to, $subject, $body);

        return self::getMailer()->send($message);
    }
    
    public function validationContrat($vrac, $destinataire, $filePath) 
    {
        $from = self::getFrom();
        $to = array($destinataire);
        $proprietaire = $vrac->getCreateurInformations();
        $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
        $subject = '[Contrat vrac] Validation du contrat n° '.$vrac->numero_visa.' ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
        $body = self::getBodyFromPartial('vrac_validation_contrat', array('vrac' => $vrac));        
		$message = Swift_Message::newInstance()
  					->setFrom($from)
  					->setTo($to)
  					->setSubject($subject)
  					->setBody($body)
  					->attach(Swift_Attachment::fromPath($filePath));
		
        return self::getMailer()->send($message);
    }
    
    public function annulationContrat($vrac, $destinataire) 
    {
        $from = self::getFrom();
        $to = array($destinataire);
        $proprietaire = $vrac->getCreateurInformations();
        $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
        $subject = '[Contrat vrac] Annulation ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
        $body = self::getBodyFromPartial('vrac_annulation_contrat', array('vrac' => $vrac));
        $message = self::getMailer()->compose($from, $to, $subject, $body);

        return self::getMailer()->send($message);
    }
    
    public function clotureContrat($vrac, $destinataire, $filePath) 
    {
        $from = self::getFrom();
        $to = array($destinataire);
        $proprietaire = $vrac->getCreateurInformations();
        $proprietaireLibelle = ($proprietaire->intitule)? $proprietaire->intitule.' '.$proprietaire->raison_sociale : $proprietaire->raison_sociale;
        $subject = '[Contrat vrac] Cloture du contrat n° '.$vrac->numero_visa.' ('.$proprietaireLibelle.' – créé le '.strftime('%d/%m', strtotime($vrac->valide->date_saisie)).')';
        $body = self::getBodyFromPartial('vrac_cloture_contrat', array('vrac' => $vrac));        
		$message = Swift_Message::newInstance()
  					->setFrom($from)
  					->setTo($to)
  					->setSubject($subject)
  					->setBody($body)
  					->attach(Swift_Attachment::fromPath($filePath));
		
        return self::getMailer()->send($message);
    }
    
    protected static function getFrom()
    {
    	return array('teledeclaration@civa.fr' => "Contrats CIVA");
    }

    protected static function getMailer() 
    {
        return sfContext::getInstance()->getMailer();
    }

    protected static function getBodyFromPartial($partial, $vars = null) 
    {
        return sfContext::getInstance()->getController()->getAction('email', 'main')->getPartial('email/' . $partial, $vars);
    }

}