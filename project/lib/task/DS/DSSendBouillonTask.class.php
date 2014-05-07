<?php

class DSSendBrouillonTask extends sfBaseTask
{

    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('campagne', sfCommandArgument::REQUIRED, 'campagne'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'ds';
        $this->name = 'send-brouillons';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [ds:send-brouillons] task envoie des DS brouillons.
Call it with:

  [php symfony ds:send-brouillons|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        sfContext::createInstance($this->configuration);
        $comptes = _CompteClient::getInstance()->getAll();
        foreach ($comptes as $id => $compteView) {
            $compte = _CompteClient::getInstance()->findByLogin(str_replace('COMPTE-', '', $id));
            if($compte->isActif()){
             foreach($compte->getTiersObject() as $tiers) {
                 var_dump($tiers); exit;
                 //checker tiers has droit DS
                    $cvi = str_replace("REC-", "", $tiers->id);
                    echo $this->green('Traitement : ')." creation de mail pour le RECOLTANT ".$this->green($tiers->id)."\n";
                    $rec = RecoltantClient::getInstance()->find($tiers->id); 
                    $this->executeSendMail($rec);
                    continue;
                }
            }
        }
    }
    
    public function executeSendMail($tier)
    {
        $mail = $this->sendBrouillon($tier);
            if($mail){
               echo $this->green('SUCESS : ')."le mail pour le tiers ".$tier->cvi." a été envoyé à l'adresse email : ".$this->green($mail).".\n";
            }else{
               echo $this->red('ERROR : ')."le mail pour le tiers ".$tier->cvi." a échoué.\n";
            }
    }
    
    public function sendBrouillon($tier)
    {
        $document = null;
        try{
        $document = new ExportDSPdfEmpty($tier, array($this, 'getPartial'), true, 'pdf');        
        } 
        catch (sfException $e){
            echo $this->red('[ABSENCE DE LIEUX DE STOCKAGE] ');
            return false;
        }
       $document->removeCache();
       $document->generatePDF();
       
       $pdfContent = $document->output();

       $mess = 'Bonjour ' . $tier->nom . ',
Vous trouverez ci-joint votre Déclaration de Stocks brouillon pour l\'année ' . date('Y') . '.
Ce document constitue un exemple à remplir.

Cordialement,

Le CIVA';
       $email = $tier->getCompteEmail();
       if(!$email){
            echo $this->yellow('WARNING : ')."le tiers ".$tier->cvi." ne possède pas d'email.\n";
            return false;
        }
        
        $message = Swift_Message::newInstance()
                ->setFrom(array('ne_pas_repondre@civa.fr' => "Webmaster Vinsalsace.pro"))
                //->setTo($email)
                ->setTo("mpetit@actualys.com")
                ->setSubject('CIVA - Exemple de déclaration de Stocks')
                ->setBody($mess);


        $attachment = new Swift_Attachment($pdfContent, $document->getFileName(), 'application/pdf');
        $message->attach($attachment);
        
        try {
            $this->getMailer()->send($message);
        } catch (Exception $e) {

            return false;
        }
        
        return $email;
    }
    
    public function getPartial($templateName, $vars = null) {
        $this->configuration->loadHelpers('Partial');
        $vars = null !== $vars ? $vars : $this->varHolder->getAll();
        return get_partial($templateName, $vars);
    }
    
    public function green($string) {
        return "\033[32m".$string."\033[0m";
    }
        
    public function yellow($string) {
        return "\033[33m".$string."\033[0m";
    }
    
    public function red($string) {
        return "\033[31m".$string."\033[0m";
    }
}