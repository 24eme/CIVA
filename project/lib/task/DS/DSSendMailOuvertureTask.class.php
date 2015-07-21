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
            new sfCommandArgument('id_compte', sfCommandArgument::REQUIRED, 'id_compte'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            new sfCommandOption('dryrun', null, sfCommandOption::PARAMETER_REQUIRED, 'Dry Run', false),
        ));

        $this->namespace = 'ds';
        $this->name = 'send-mail-ouverture';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [ds:send-brouillons] task envoie du mail d'ouverture
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
        if(!array_key_exists('periode',$arguments) || !preg_match('/^([0-9]{6})$/', $arguments['periode'])){
            throw new sfException("La periode doit être passé en argument et doit être de la forme AAAAMM");
        }
        $this->periode = $arguments['periode'];

        $compte = _CompteClient::getInstance()->find($arguments["id_compte"]);

        if(!$compte){
            
            return;
        }
        if($compte->type != "CompteTiers") {
            
            return;
        }

        if(!$compte->isActif()){
            
            return;
        }

        if(!$compte->isInscrit()){
            
            return;
        }

        if(!$compte->email){
            
            return;
        }

        if(!$compte->hasDroit(_CompteClient::DROIT_DS_DECLARANT)) {
            return;
        }

        $this->executeSendMail($compte, DSCivaClient::TYPE_DS_PROPRIETE, $options['dryrun']);
        $this->executeSendMail($compte, DSCivaClient::TYPE_DS_NEGOCE, $options['dryrun']);
    }
    
    public function executeSendMail($compte, $type_ds, $dryrun = false)
    {   
        $tiers = $compte->getDeclarantDS($type_ds);

        if(!$tiers) {

            return;
        }

        if(!$tiers->hasLieuxStockage() && !$tiers->isAjoutLieuxDeStockage()) {

            echo $type_ds.";ERROR;PAS DE LIEU DE STOCKAGE;".$compte->_id."\n";
            return;
        }

        // Exclusion des ds négoce des comptes acheteurs inscrit sur le metteur en marché 
        if($tiers->type == "MetteurEnMarche" && $tiers->cvi && $tiers->cvi == $compte->login) {
            $tiersAchat = AcheteurClient::getInstance()->findByCvi($tiers->cvi);
            if($tiersAchat) {
                $compteAchat = $tiersAchat->getCompteObject();
                $compteMetteurEnMarche = $tiers->getCompteObject();

                if($compte->_id == $compteAchat->_id && $compteMetteurEnMarche->isInscrit() && $compteMetteurEnMarche->email && $compteMetteurEnMarche->isActif() && $compteMetteurEnMarche->hasDroit(_CompteClient::DROIT_DS_DECLARANT)) {

                    return;
                }
            }
        }

        $ds = $tiers->getDs($this->periode);
        $teledeclarant = ($ds && (!$ds->exist("date_depot_mairie") || !$ds->get("date_depot_mairie")));
        $recuperationDoc = ($ds && $type_ds == DSCivaClient::TYPE_DS_PROPRIETE);
        $email = $compte->email;

        $log = sprintf("%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s", $type_ds, $teledeclarant, $recuperationDoc, !is_null($ds), $compte->login, $compte->email, $tiers->cvi, $tiers->civaba, $tiers->categorie, $tiers->qualite_categorie, $tiers->nom, $tiers->siege->commune, $compte->_id, $tiers->_id);

        if($dryrun) {
            echo $log.";0\n";
            return;
        }

        if($recuperationDoc) {
            $document = $this->getPdfDocument($tiers, $type_ds);
            if(!$document) {
                return false;
            }
            $pdfContent = $document->output();
        }

        $message = Swift_Message::newInstance()
                ->setFrom(array('ne_pas_repondre@civa.fr' => "Webmaster Vinsalsace.pro"))
                ->setTo($email);

        $this->configureMessage($message, $type_ds, $teledeclarant);

        $attachment = new Swift_Attachment(file_get_contents(sfConfig::get('sf_data_dir')."/pdf/votre_declaration_de_stocks_pas_a_pas.pdf"), "votre_declaration_de_stocks_pas_a_pas.pdf", 'application/pdf');
            $message->attach($attachment);

        if($document && $pdfContent) {
            $attachment = new Swift_Attachment($pdfContent, $document->getFileName(), 'application/pdf');
            $message->attach($attachment);
        }

        try {
            $this->getMailer()->send($message);
        } catch (Exception $e) {
            echo $log.";0;".$e->getMessage()."\n";
            return false;
        }

        echo $log.";1\n";
    }

    protected function configureMessage($message, $type_ds, $teledeclarant) {

        if($type_ds == DSCivaClient::TYPE_DS_PROPRIETE && $teledeclarant) {

            $message->setSubject('Déclaration de Stocks "Propriété" au 31 Juillet 2015')
                    ->setBody("Bonjour,

Vous avez télé-déclaré votre Stock 2014 sur le Portail du CIVA et nous n'avons donc pas pré-identifié de formulaire pour votre entreprise en Mairie.

Si vous optez à nouveau pour cette solution, la procédure pour la télé-déclaration des Stocks au 31 Juillet 2014 sera accessible à compter du 27 juillet et vous n'avez donc aucun document à remettre en Mairie.

Attention la date limite de télé-déclaration est fixée par les Douanes au 31 Août minuit.

Pour vous aider dans votre démarche vous trouverez ci-joint un brouillon personnalisé de votre DS 2014, qui reprend les produits théoriquement détenus en stocks.

Ce document constitue une aide à la télé-déclaration et n'est en aucun cas à retourner au CIVA.

Cordialement,

Le CIVA");

            return $message;
        }

        if($type_ds == DSCivaClient::TYPE_DS_PROPRIETE && !$teledeclarant) {

            $message->setSubject('Déclaration de Stocks "Propriété" au 31 Juillet 2015')
                    ->setBody("Bonjour,

En 2013 vous avez déposé une Déclaration de Stocks \"papier\", nous allons donc envoyer en Mairie un formulaire pré-identifié pour votre entreprise.

Si néanmoins, vous souhaitez cette année télé-déclarer votre Stock au 31 Juillet 2014 sur le Portail CIVA, le télé-service \"Alsace Stocks\" sera accessible à compter du 1er juillet et vous n'aurez donc aucun document à remettre en Mairie.

Attention la date limite de télé-déclaration est fixée par les Douanes au 31 Août minuit.

Pour vous aider dans votre démarche vous trouverez ci-joint, un document explicatif \"Pas à Pas\", ainsi qu'un brouillon personnalisé de votre DS 2014, qui reprend les produits théoriquement détenus en stocks.

Ce document constitue une aide à la télé-déclaration et n'est en aucun cas à retourner au CIVA.

Cordialement,

Le CIVA");

            return $message;
        }

        if($type_ds == DSCivaClient::TYPE_DS_NEGOCE && $teledeclarant) {

            $message->setSubject('Déclaration de Stocks "Négoce" au 31 Juillet 2014')
                    ->setBody("Bonjour 

Vous recevrez dans les prochains jours votre Déclaration de Stocks au 31 Juillet 2014 à retourner au CIVA au plus tard le 1er Septembre.

A compter de cette année vous avez la possibilité de télé-déclarer sur le Portail CIVA, votre Stock au 31 Juillet voire celui au 31 Décembre si vous êtes concerné.

Le télé-service \"Alsace Stocks\" sera accessible du 1er juillet au 10 Septembre inclus, et vous n'aurez donc pas à renvoyer le formulaire papier au CIVA.

Pour vous aider dans votre démarche vous pourrez télécharger la Notice d'Aide au format PDF ou consulter l'aide en ligne.

Cordialement,

Le CIVA");

            return $message;
        }

        if($type_ds == DSCivaClient::TYPE_DS_NEGOCE && !$teledeclarant) {

            $message->setSubject('Déclaration de Stocks "Négoce" au 31 Juillet 2014')
                    ->setBody("Bonjour 

Vous recevrez dans les prochains jours votre Déclaration de Stocks au 31 Juillet 2014 à retourner au CIVA au plus tard le 1er Septembre.

A compter de cette année vous avez la possibilité de télé-déclarer sur le Portail CIVA, votre Stock au 31 Juillet voire celui au 31 Décembre si vous êtes concerné.

Le télé-service \"Alsace Stocks\" sera accessible du 1er juillet au 10 Septembre inclus, et vous n'aurez donc pas à renvoyer le formulaire papier au CIVA.

Pour vous aider dans votre démarche vous pourrez télécharger la Notice d'Aide au format PDF ou consulter l'aide en ligne.

Cordialement,

Le CIVA");

            return $message;
        }
    }

    protected function getPdfDocument($tiers, $type_ds) {
        $document = null;
        try{
            $document = new ExportDSPdfEmpty($tiers, $type_ds, array($this, 'getPartial'), true, 'pdf');        
        } 
        catch (sfException $e){
            echo $this->red('[ABSENCE DE LIEUX DE STOCKAGE] '.$e->getMessage());
            return false;
        }
        $document->removeCache();
        $document->generatePDF();

        return $document;
    }

    
    public function getPartial($templateName, $vars = null) {
        $this->configuration->loadHelpers('Partial');
        $vars = null !== $vars ? $vars : $this->varHolder->getAll();
        return get_partial($templateName, $vars);
    }
}
