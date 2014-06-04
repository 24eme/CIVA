<?php

class DSSendBrouillonTask extends sfBaseTask
{

    protected $debug = false;
    
    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('periode', sfCommandArgument::REQUIRED, 'periode'),
            new sfCommandArgument('debug', sfCommandArgument::REQUIRED, '0'),
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
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        $this->debug = array_key_exists('debug', $arguments) && ((bool) $arguments['debug']);
        if(!array_key_exists('periode',$arguments) || !preg_match('/^([0-9]{6})$/', $arguments['periode'])){
            throw new sfException("La periode doit être passé en argument et doit être de la forme AAAAMM");
        }
        $periode = $arguments['periode'];
        $recoltants_identifiant = $this->getIdentifiantRecByPeriode($periode);
        foreach ($recoltants_identifiant as $id => $cvi) {
            $tiers = acCouchdbManager::getClient("_Tiers")->findByIdentifiant($cvi);
            if(!$tiers){
                echo "Aucun tiers trouvé pour ".$cvi."\n";
                continue;
            }
            $compte = $tiers->getCompteObject();
            if(!$compte){
                 echo "Aucun compte trouvé pour ".$tiers->_id."\n";
                 continue;
            }
            if($tiers->getCompteObject()->isActif() && $tiers->isDeclarantStockPropriete()){
                 //checker tiers has droit DS
                if($this->debug){
                 echo $tiers->getIdentifiant().",".$tiers->getCompteEmail()."\n";
                }else{
                   echo $this->green('Traitement : ')." creation de mail pour le RECOLTANT ".$this->green($tiers->_id)."\n";
                   $this->executeSendMail($tiers);               
                }
            }
        }
    }
    
    public function executeSendMail($tiers)
    {
        $mail = $this->sendBrouillon($tiers);
            if($mail){
               echo $this->green('SUCESS : ')."le mail pour le tiers ".$tiers->getIdentifiant()." a été envoyé à l'adresse email : ".$this->green($mail).".\n";
            }else{
               echo $this->red('ERROR : ')."le mail pour le tiers ".$tiers->getIdentifiant()." a échoué.\n";
            }
    }
    
    public function sendBrouillon($tiers)
    {
        $document = null;
        try{
        $document = new ExportDSPdfEmpty($tiers, array($this, 'getPartial'), true, 'pdf');        
        } 
        catch (sfException $e){
            echo $this->red('[ABSENCE DE LIEUX DE STOCKAGE] ');
            return false;
        }
       $document->removeCache();
       $document->generatePDF();
       
       $pdfContent = $document->output();

       $mess = 'Bonjour ' . $tiers->nom . ',
Vous trouverez ci-joint votre Déclaration de Stocks brouillon pour l\'année ' . date('Y') . '.
Ce document constitue un exemple de Déclaration de Stocks, il n\'est donc pas à remplir ni à renvoyer au CIVA.

Cordialement,

Le CIVA';
       $email = $tiers->getCompteEmail();
       if(!$email){
            echo $this->yellow('WARNING : ')."le tiers ".$tiers->getIdentifiant()." ne possède pas d'email.\n";
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
    
    private function getIdentifiantRecByPeriode($periode)
    {
        $campagne_view = (substr($periode,0,4)-1) .'-'.substr($periode,0,4);
        $ds_validees_view = acCouchdbManager::getClient()->reduce(false)
                                                    ->startkey(array($campagne_view))
                                                    ->endkey(array($campagne_view,array()))
                                                    ->getView("STATS", "DS")->rows;
        
        $recoltantId = array();
        foreach ($ds_validees_view as $ds_validee) {
            if($ds_validee->key[5]){
                continue;
            }
            $identifiant = substr($ds_validee->id,3,10);
            $recoltantId[$identifiant] = $identifiant;
        }
        return $recoltantId;
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