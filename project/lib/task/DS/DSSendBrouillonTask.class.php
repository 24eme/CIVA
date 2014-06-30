<?php

class DSSendBrouillonTask extends sfBaseTask
{

    protected $debug = false;
    protected $periode = null;
    
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
        $this->periode = $arguments['periode'];

        $ids = _CompteClient::getInstance()->getAll(acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
        foreach ($ids as $id) {
            $compte = _CompteClient::getInstance()->find($id);
            if(!$compte){
                //echo "Aucun compte trouvé pour ".$id."\n";
                continue;
            }
            if($compte->type != "CompteTiers") {
                continue;
            }

            $tiers = $compte->getDeclarantDS();
            if(!$tiers){
                 //echo "Le compte ne fait pas de stock ".$compte->getTiersType()->_id.":".$compte->getTiersType()->categorie."\n";
                 continue;
            }
            if(!$compte->isActif()){
                continue;
            }

            if(!$compte->email){
                //echo $this->yellow('WARNING : ')."le tiers ".$tiers->getIdentifiant()." ne possède pas d'email.\n";
                continue;
            }
             //checker tiers has droit DS
            if($this->debug){
             echo $compte->getLogin().",".$compte->getEmail().",".$tiers->getTypeDS()."\n";
            }else{
               
               $this->executeSendMail($tiers, $compte);               
            }
            
        }
    }
    
    public function executeSendMail($tiers, $compte)
    {   
        $mail = false;
        $typeDS = $tiers->getTypeDS();
        if($typeDS == DSCivaClient::TYPE_DS_PROPRIETE) {
            $mail = $this->sendPropriete($tiers, $compte);
        }
        if($typeDS == DSCivaClient::TYPE_DS_NEGOCE) {
            $mail = $this->sendNegoce($tiers, $compte);
        }

        if($mail){
           echo $this->green('SUCESS : ')."le mail pour le tiers ".$tiers->getIdentifiant()." a été envoyé à l'adresse email : ".$this->green($mail).".\n";
        }else{
           echo $this->red('ERROR : ')."le mail pour le tiers ".$tiers->getIdentifiant()." a échoué.\n";
        }
    }

    public function sendPropriete($tiers, $compte) {
        $ds = DSCivaClient::getInstance()->findByIdentifiantAndPeriode($tiers->cvi, $this->periode);

        if(!$ds) {

            echo "Pas de DS en 2013 : ".$compte->_id."\n";
            return false;
        }

        if(!$ds->exist("date_depot_mairie") || !$ds->get("date_depot_mairie")) {
            
            return $this->sendProprieteTeledeclarant($tiers, $compte);
        } 
        
        return $this->sendProprieteNonTeledeclarant($tiers, $compte);
    }

    protected function getPdfDocument($tiers) {
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

        return $document;
    }

    public function sendProprieteTeledeclarant($tiers, $compte) {
        echo $this->green('Traitement:')." creation de mail ".$this->green("propriété")." " . $this->yellow("télédéclarant") . " ".$this->green($tiers->_id)."\n";
        $email = $compte->email;

        $document = $this->getPdfDocument($tiers);
        if(!$document) {
            return false;
        }
        $pdfContent = $document->output();

        $mess = "Bonjour " . $tiers->nom . "

Vous avez télé-déclaré votre Stock 2013 sur le Portail du CIVA et nous n'avons donc pas pré-identifié de formulaire pour votre entreprise en Mairie.

Si vous optez à nouveau pour cette solution, la procédure pour la télé-déclaration des Stocks au 31 Juillet 2014 sera accessible à compter du 1er juillet et vous n'avez donc aucun document à remettre en Mairie.

Attention la date limite de télé-déclaration est fixée par les Douanes au 31 Août minuit.

Pour vous aider dans votre démarche vous trouverez ci-joint un brouillon personnalisé de votre DS 2014, qui reprend les produits théoriquement détenus en stocks.

Ce document constitue une aide à la télé-déclaration et n'est en aucun cas à retourner au CIVA.

Cordialement,

Le CIVA";
        
        $message = Swift_Message::newInstance()
                ->setFrom(array('ne_pas_repondre@civa.fr' => "Webmaster Vinsalsace.pro"))
                //->setTo($email)
                ->setTo("vlaurent@actualys.com")
                ->setSubject('Déclaration de Stocks "Propriété" au 31 Juillet 2014')
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

    public function sendProprieteNonTeledeclarant($tiers, $compte) {
        echo $this->green('Traitement:')." creation de mail ".$this->green("propriété"). " " . $this->red("non")." télédeclarant ".$this->green($tiers->_id)."\n";

        $email = $compte->email;

        $document = $this->getPdfDocument($tiers);
        if(!$document) {
            return false;
        }
        $pdfContent = $document->output();

       if(!$pdfContent) {
            return false;
       }

        $mess = "Bonjour " . $tiers->nom . "

En 2013 vous avez déposé une Déclaration de Stocks \"papier\", nous allons donc envoyer en Mairie un formulaire pré-identifié pour votre entreprise.

Si néanmoins, vous souhaitez cette année télé-déclarer votre Stock au 31 Juillet 2014 sur le Portail CIVA , le télé-service \"Alsace Stocks\" sera accessible à compter du 1er juillet et vous n'aurez donc aucun document à remettre en Mairie.

Attention la date limite de télé-déclaration est fixée par les Douanes au 31 Août minuit.

Pour vous aider dans votre démarche vous trouverez ci-joint, un document  explicatif \"Pas à Pas\", ainsi qu'un brouillon personnalisé de votre DS 2014, qui reprend les produits théoriquement détenus en stocks.

Ce document constitue une aide à la télé-déclaration et n'est en aucun cas à retourner au CIVA.

Cordialement,

Le CIVA";
        
        $message = Swift_Message::newInstance()
                ->setFrom(array('ne_pas_repondre@civa.fr' => "Webmaster Vinsalsace.pro"))
                //->setTo($email)
                ->setTo("vlaurent@actualys.com")
                ->setSubject('Déclaration de Stocks "Propriété" au 31 Juillet 2014')
                ->setBody($mess);

        $attachment = new Swift_Attachment($pdfContent, $document->getFileName(), 'application/pdf');
        $message->attach($attachment);

        $attachment = new Swift_Attachment(file_get_contents(sfConfig::get('sf_data_dir')."/pdf/votre_declaration_de_stocks_pas_a_pas.pdf"), $document->getFileName(), 'application/pdf');
        $message->attach($attachment);
        
        try {
            $this->getMailer()->send($message);
        } catch (Exception $e) {

            return false;
        }
        
        return $email;
    }

    public function sendNegoce($tiers, $compte) {
        echo $this->green('Traitement:')." creation de mail ".$this->green("négoce")." télédeclarant ".$this->green($tiers->_id)."\n";
        $email = $compte->email;
        $mess = "Bonjour 

Vous recevrez dans les prochains jours votre Déclaration de Stocks au 31 Juillet 2014 à retourner au CIVA au plus tard le 1er Septembre.

A compter de cette année vous avez la possibilité de télé-déclarer sur le Portail CIVA, votre Stock au 31 Juillet voire celui au 31 Décembre si vous êtes concerné.

Le télé-service \"Alsace Stocks\" sera accessible du 1er juillet au 10 Septembre inclus, et vous n'aurez donc pas à renvoyer le formulaire papier au CIVA.

Pour vous aider dans votre démarche vous pourrez télécharger la Notice d'Aide au format PDF ou consulter l'aide en ligne.

Cordialement,

Le CIVA";
        
        $message = Swift_Message::newInstance()
                ->setFrom(array('ne_pas_repondre@civa.fr' => "Webmaster Vinsalsace.pro"))
                //->setTo($email)
                ->setTo("vlaurent@actualys.com")
                ->setSubject('Déclaration de Stocks "Négoce" au 31 Juillet 2014')
                ->setBody($mess);

        try {
            $this->getMailer()->send($message);
        } catch (Exception $e) {

            return false;
        }
        
        sleep(1000);

        return $email;
    }
    
    public function sendBrouillon($tiers, $compte)
    {
  
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