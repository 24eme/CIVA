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
        //$to = array($destinataire);
        $to = array('jblemetayer@actualys.com');
        $proprietaire = $vrac->getCreateurInformations();
        $subject = 'Demande de signature d\'un contrat en vrac de la part de '.$proprietaire->raison_sociale;
        $body = self::getBodyFromPartial('vrac_demande_signature', array('vrac' => $vrac));
        $message = self::getMailer()->compose($from, $to, $subject, $body)->setContentType('text/html');

        return self::getMailer()->send($message);
    }
    
    public function confirmationSignature($vrac, $destinataire) 
    {
        $from = self::getFrom();
        //$to = array($destinataire);
        $to = array('jblemetayer@actualys.com');
        $subject = 'Confirmation de signature d\'un contrat en vrac';
        $body = self::getBodyFromPartial('vrac_confirmation_signature', array('vrac' => $vrac));
        $message = self::getMailer()->compose($from, $to, $subject, $body)->setContentType('text/html');

        return self::getMailer()->send($message);
    }
    
    public function validationContrat($vrac, $destinataire, $filePath) 
    {
        $from = self::getFrom();
        //$to = array($destinataire);
        $to = array('jblemetayer@actualys.com');
        $subject = 'Validation d\'un contrat en vrac';
        $body = self::getBodyFromPartial('vrac_validation_contrat', array('vrac' => $vrac));        
		$message = Swift_Message::newInstance()
  					->setFrom($from)
  					->setTo($to)
  					->setSubject($subject)
  					->setBody($body)
  					->setContentType('text/html')
  					->attach(Swift_Attachment::fromPath($filePath));
		
        return self::getMailer()->send($message);
    }
    
    public function annulationContrat($vrac, $destinataire) 
    {
        $from = self::getFrom();
        //$to = array($destinataire);
        $to = array('jblemetayer@actualys.com');
        $subject = 'Annulation d\'un contrat en vrac';
        $body = self::getBodyFromPartial('vrac_annulation_contrat', array('vrac' => $vrac));
        $message = self::getMailer()->compose($from, $to, $subject, $body)->setContentType('text/html');

        return self::getMailer()->send($message);
    }
    
    public function expirationContrat($vrac, $destinataire) 
    {
        $from = self::getFrom();
        //$to = array($destinataire);
        $to = array('jblemetayer@actualys.com');
        $subject = 'Expiration d\'un contrat en vrac';
        $body = self::getBodyFromPartial('vrac_expiration_contrat', array('vrac' => $vrac));
        $message = self::getMailer()->compose($from, $to, $subject, $body)->setContentType('text/html');

        return self::getMailer()->send($message);
    }
    
    protected static function getFrom()
    {
    	return array('ne_pas_repondre@civa.fr' => "Webmaster Vinsalsace.pro");
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