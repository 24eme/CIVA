<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class RecolteMailingManager
 * @author mathurin
 */
class RecolteMailingManager {
     
    protected $mailer = null;
    protected $partial_function = null;

    protected $dr = null;
    protected $tiers = null;
    protected $annee = null;
    protected $current_document = null;
    protected $current_pdf_content = null;
    protected $csvContent = null;
    protected $acheteurs = null;



    public function __construct($mailer,$partial_fct, $dr,$tiers,$annee) {
        $this->mailer = $mailer;
        $this->partial_function = $partial_fct;
        $this->dr = $dr;
        $this->tiers = $tiers;
        $this->annee = $annee;
        
        $this->csvContent = $this->getCSVDrContent();
        
        $this->acheteurs = $this->dr->recolte->getAcheteursArray();
        $this->current_document = new ExportDRPdf($this->dr, $this->partial_function);
        $this->current_document->generatePDF();
        $this->current_pdf_content = $this->current_document->output();
        
    }
    
    public function setDR($dr) {
        $this->dr = $dr;
    }
    
    public function getDR() {
        return $this->dr;
    }
    
    
    public function sendMail($visualisation = true) {
        $message = $this->getMailForRecoltant($visualisation);
        try {
            $this->mailer->send($message);
        } catch (Exception $e) {

            return false;
        }
        return true;
    }
    
    public function sendAcheteursMails() {                
      $sendMailAcheteursReport = array();
        
      foreach ($this->acheteurs as $type_cvi => $vol) {
            $type_cvi_infos = explode('_', $type_cvi);
            $sendMailAcheteursReport[$type_cvi] = new stdClass();
            $sendMailAcheteursReport[$type_cvi]->type = $type_cvi_infos[0];
            $sendMailAcheteursReport[$type_cvi]->cvi = $type_cvi_infos[1];
            
            $acheteur = _TiersClient::getInstance()->retrieveByCvi($sendMailAcheteursReport[$type_cvi]->cvi);
            
            $sendMailAcheteursReport[$type_cvi]->nom = $acheteur->nom;
            
            $email = $acheteur->getCompteEmail();
            if(!$email) {
                $email = $acheteur->email;
            }
            $sendMailAcheteursReport[$type_cvi]->email = $email;
            $message = $this->getMailForAcheteur($acheteur);
        
            try {
                $this->mailer->send($message);
            } catch (Exception $e) {
               $sendMailAcheteursReport[$type_cvi]->sended = false;
            }
            $sendMailAcheteursReport[$type_cvi]->sended = true;
        }
        return $sendMailAcheteursReport;
    }
    
    public function getCSVDrContent(){ 
         $csvContruct = new ExportDRCsv($this->annee,$this->tiers->cvi);         
         $csvContruct->export();
         return $csvContruct->output();
        }
    
        
    public function getMailForAcheteur($acheteur) {
        
            $subject = 'CIVA - Déclaration de récolte de '.$this->tiers->nom;   
        
            $mess = 'Bonjour,
         
Le vendeur de raisin '.$this->tiers->nom.' a souhaité vous faire parvenir sa déclaration de récolte pour l\'année ' . $this->annee .'.
    
Vous trouverez ce document en pièce jointe aux formats PDF et CSV.

--
L\'application de télédéclaration de récolte du CIVA';

        $email = $acheteur->getCompteEmail();
        if(!$email) {
            $email = $acheteur->email;
        }

        $message = Swift_Message::newInstance()
                ->setFrom(array('ne_pas_repondre@civa.fr' => "Webmaster Vinsalsace.pro"))
                ->setTo($email)
                ->setSubject($subject)
                ->setBody($mess);        
        
        
        $attachment_pdf = new Swift_Attachment($this->current_pdf_content, $this->current_document->getFileName(), 'application/pdf');
        $message->attach($attachment_pdf);
        
        if($this->csvContent) {
            $attachment_csv = new Swift_Attachment($this->csvContent, sprintf("DR_%s_%s", $this->tiers->cvi, $this->annee).'.csv', 'application/csv');
            $message->attach($attachment_csv);
        }
        return $message;
    }
        
    public function getMailForRecoltant($visualisation = true) {
                // si l'on vient de la page de visualisation
        if($visualisation)
        {
            $subject = 'CIVA - Votre déclaration de récolte';
            $mess = 'Bonjour,

Vous trouverez ci-joint votre déclaration de récolte pour l\'année ' . $this->annee . '.

Cordialement,

Le CIVA';

        }else{

        $subject = 'CIVA - Validation de votre déclaration de récolte';
        $mess = 'Bonjour,

Vous venez de valider votre déclaration de récolte pour l\'année ' . date("Y") . '.
    
Vous trouverez ci-joint votre déclaration de récolte au format PDF et au format Tableur.

Vous pouvez également toujours la visualiser sur votre espace civa : ' . sfConfig::get('app_base_url') . 'mon_espace_civa

--
L\'application de télédéclaration de récolte du CIVA';

        }

        //send email


        $message = Swift_Message::newInstance()
                ->setFrom(array('ne_pas_repondre@civa.fr' => "Webmaster Vinsalsace.pro"))
                ->setTo($this->tiers->getCompteEmail())
                ->setSubject($subject)
                ->setBody($mess);


        $attachment_pdf = new Swift_Attachment($this->current_pdf_content, $this->current_document->getFileName(), 'application/pdf');
        $message->attach($attachment_pdf);
        
        if($this->csvContent) {
            $attachment_csv = new Swift_Attachment($this->csvContent, sprintf("DR_%s_%s", $this->tiers->cvi, $this->annee).'.csv', 'application/csv');
            $message->attach($attachment_csv);
        }
        return $message;      
    }    
    
    protected function getPartial($templateName, $vars = null) {
      return call_user_func_array($this->partial_function, array($templateName, $vars));
    }
}
