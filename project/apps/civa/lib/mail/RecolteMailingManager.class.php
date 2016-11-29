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

        $this->acheteurs = DRClient::getInstance()->getAcheteursApporteur($this->dr->cvi, $this->dr->campagne);
        $this->current_document = new ExportDRPdf($this->dr, $this->partial_function);
        $this->current_document->generatePDF();
        $this->current_pdf_content = $this->current_document->output();

    }

    public function getAcheteurs() {
        return $this->acheteurs;
    }

    public function setDR($dr) {
        $this->dr = $dr;
    }

    public function getDR() {
        return $this->dr;
    }


    public function sendMail($visualisation = true) {
        $message = $this->getMailForRecoltant($visualisation);
        $this->mailer->send($message);

        return true;
    }

    public function sendAcheteursMails() {
      $sendMailAcheteursReport = array();

      foreach ($this->getAcheteurs() as $acheteur) {
            $type_cvi = $acheteur->acheteur_raisin."_".$acheteur->cvi;

            $sendMailAcheteursReport[$type_cvi] = new stdClass();
            $sendMailAcheteursReport[$type_cvi]->type = $acheteur->acheteur_raisin;
            $sendMailAcheteursReport[$type_cvi]->cvi = $acheteur->cvi;
            $sendMailAcheteursReport[$type_cvi]->nom = $acheteur->nom;

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
L\'application de télédéclaration de récoltes du CIVA';

        $email = null;
        try {
            $oldAcheteur = _TiersClient::getInstance()->find("ACHAT-".$acheteur->cvi);
            if($oldAcheteur) {
                $email = $oldAcheteur->getEmailByDroit(_CompteClient::DROIT_DR_ACHETEUR);
            }
        } catch (Exception $e) {

        }

        if(!$email) {
            $email = $acheteur->getEmailTeledeclaration();
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

--
L\'application de télédéclaration de récoltes du CIVA';

        }else{

        $subject = 'CIVA - Validation de votre déclaration de récolte';
        $mess = $this->getMessageValidation($this->dr);

        }

        $message = Swift_Message::newInstance()
                ->setFrom(array('ne_pas_repondre@civa.fr' => "Webmaster Vinsalsace.pro"))
                ->setTo($this->tiers->getEmailTeledeclaration())
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

    protected function getMessageValidation($dr) {

        if($dr->exist('validee_par') && $dr->validee_par && !in_array($dr->validee_par, array(DRClient::VALIDEE_PAR_RECOLTANT, DRClient::VALIDEE_PAR_CIVA))) {

            return 'Bonjour,

Votre déclaration de récolte pour l\'année ' . date("Y") . ' a été validée par ' . $dr->validee_par . '.

Vous trouverez ci-joint votre déclaration de récolte au format PDF et au format Tableur.

Vous pouvez également toujours la visualiser sur votre espace civa : ' . preg_replace("|/$|", "", sfConfig::get('app_base_url')) . '/mon_espace_civa/'.$dr->identifiant.'

--
L\'application de télédéclaration de récoltes du CIVA';

        }

        return 'Bonjour,

Vous venez de valider votre déclaration de récolte pour l\'année ' . date("Y") . '.

Vous trouverez ci-joint votre déclaration de récolte au format PDF et au format Tableur.

Vous pouvez également toujours la visualiser sur votre espace civa : '  . preg_replace("|/$|", "", sfConfig::get('app_base_url')) . '/mon_espace_civa/'.$dr->identifiant.'

--
L\'application de télédéclaration de récoltes du CIVA';
    }

    protected function getPartial($templateName, $vars = null) {
      return call_user_func_array($this->partial_function, array($templateName, $vars));
    }
}
