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
            new sfCommandArgument('id_etablissement', sfCommandArgument::REQUIRED, 'id_etablissement'),
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
        $this->detailedDescription = "";
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
        $this->is_decembre = preg_match("/12$/", $this->periode);

        $etablissement = CompteClient::getInstance()->find($arguments["id_etablissement"], acCouchdbClient::HYDRATE_JSON);

        if(!$etablissement){

            return;
        }

        if($etablissement->statut != EtablissementClient::STATUT_ACTIF){

            return;
        }

        $etablissement = CompteClient::getInstance()->find($arguments["id_etablissement"]);

        if($etablissement->hasDroit(Roles::TELEDECLARATION_DS_PROPRIETE) && !$etablissement->hasDroit(Roles::TELEDECLARATION_DS_NEGOCE)) {

            $this->executeSendMail($etablissement, DSCivaClient::TYPE_DS_PROPRIETE, $options['dryrun']);
        }

        if($etablissement->hasDroit(Roles::TELEDECLARATION_DS_NEGOCE)) {

            $this->executeSendMail($etablissement, DSCivaClient::TYPE_DS_NEGOCE, $options['dryrun']);
        }

    }

    public function executeSendMail($etablissement, $type_ds, $dryrun = false)
    {
        $tiers = $etablissement;

        if($this->is_decembre && (!$tiers->exist('ds_decembre') || !$tiers->ds_decembre)) {

            return;
        }

        if(!$etablissement->hasLieuxStockage() && !$etablissement->isAjoutLieuxDeStockage()) {

            echo $type_ds.";ERROR;PAS DE LIEU DE STOCKAGE;".$etablissement->_id."\n";
            return;
        }


        $previousDateStock=(preg_replace("/[0-9]{2}$/", "", $this->periode)-1)."-".preg_replace("/^[0-9]{4}/", "", $this->periode)."-31";
        $ds = DSCivaClient::getInstance()->getDSPrincipale($tiers, $previousDateStock);
        $teledeclarant = ($ds && (!$ds->exist("date_depot_mairie") || !$ds->get("date_depot_mairie")));
        $recuperationDoc = ($type_ds == DSCivaClient::TYPE_DS_PROPRIETE);
        $email = $etablissement->getEmailTeledeclaration();

        $log = sprintf("%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s", $type_ds, $teledeclarant, $recuperationDoc, !is_null($ds), $email, $tiers->cvi, $tiers->num_interne, $tiers->famille, $tiers->raison_sociale, $tiers->commune, $tiers->id_societe, $tiers->_id);

        if(!$email || $dryrun) {
            echo $log.";".boolval($email).";0\n";
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

        if($document && $pdfContent) {
            $attachment = new Swift_Attachment($pdfContent, $document->getFileName(), 'application/pdf');
            $message->attach($attachment);
        }

        try {
            $this->getMailer()->send($message);
        } catch (Exception $e) {
            echo $log.";1;0;".$e->getMessage()."\n";
            return false;
        }

        echo $log.";1;1\n";

        sleep(1);
    }

    protected function configureMessage($message, $type_ds, $teledeclarant, $campagne) {

        $campagne = preg_replace("/[0-9]{2}$/", "", $this->periode);

        if($type_ds == DSCivaClient::TYPE_DS_PROPRIETE && $teledeclarant) {

            $message->setSubject("Déclaration de Stocks \"Propriété\" au 31 juillet ".$campagne)
                    ->setBody("Bonjour,

Vous avez télé-déclaré votre Stock ".($campagne - 1)." sur le Portail du CIVA <https://declaration.vinsalsace.pro> et nous n'avons donc pas pré-identifié de formulaire pour votre entreprise en Mairie.

Si vous optez à nouveau pour cette solution, la procédure pour la télé-déclaration des Stocks au 31 juillet ".$campagne." sera accessible à compter du 18 juillet et vous n'avez donc aucun document à remettre en Mairie.

Attention la date limite de la télé-déclaration est fixée par les Douanes au 10 septembre minuit.

Pour vous aider dans votre démarche vous trouverez ci-joint un brouillon personnalisé de votre DS ".$campagne.", qui reprend les produits théoriquement détenus en stocks.

Ce document constitue une aide à la télé-déclaration et n'est en aucun cas à retourner au CIVA.


Cordialement,

Le CIVA");

            return $message;
        }

        if($type_ds == DSCivaClient::TYPE_DS_PROPRIETE && !$teledeclarant) {

            $attachment = new Swift_Attachment(file_get_contents(sfConfig::get('sf_data_dir')."/pdf/votre_declaration_de_stocks_pas_a_pas_propriete.pdf"), "votre_declaration_de_stocks_pas_a_pas_propriete.pdf", 'application/pdf');
            $message->attach($attachment);

            $message->setSubject("Déclaration de Stocks \"Propriété\" au 31 juillet ".$campagne)
                    ->setBody("Bonjour,

En ".($campagne - 1).", vous avez déposé une Déclaration de Stocks \"papier\", nous avons donc envoyé en Mairie un formulaire pré-identifié pour votre entreprise.

Si néanmoins, vous souhaitez cette année télé-déclarer votre Stock au 31 juillet ".$campagne." sur le Portail CIVA <https://declaration.vinsalsace.pro>, le télé-service \"Alsace Stocks\" sera accessible à compter du 18 juillet et vous n'aurez donc aucun document à remettre en Mairie.

Attention la date limite de télé-déclaration est fixée par les Douanes au 10 septembre minuit.

Pour vous aider dans votre démarche vous trouverez ci-joint, un document explicatif \"Pas à pas\", ainsi qu'un brouillon personnalisé de votre DS ".$campagne.", qui reprend les produits théoriquement détenus en stocks.

Ce document constitue une aide à la télé-déclaration et n'est en aucun cas à retourner au CIVA.


Cordialement,

Le CIVA");

            return $message;
        }

        if($type_ds == DSCivaClient::TYPE_DS_NEGOCE && $teledeclarant) {

            $message->setSubject("Déclaration de Stocks \"Négoce\" au 31 juillet ".$campagne)
                    ->setBody("Bonjour,

Vous avez télé-déclaré votre Stock ".($campagne - 1)." sur le Portail du CIVA <https://declaration.vinsalsace.pro> et nous ne vous avons donc pas fait parvenir de formulaire pré-identifié.

Si vous optez à nouveau pour cette solution, le télé-service \"Alsace Stocks\" sera accessible du 18 juillet au 10 septembre inclus, et vous n'avez donc pas de formulaire papier à envoyer au CIVA.

Pour vous aider dans votre démarche vous pourrez télécharger la Notice d'Aide au format PDF ou consulter l'aide en ligne.


Cordialement,

Le CIVA");

            return $message;
        }

        if($type_ds == DSCivaClient::TYPE_DS_NEGOCE && !$teledeclarant) {

            $attachment = new Swift_Attachment(file_get_contents(sfConfig::get('sf_data_dir')."/pdf/votre_declaration_de_stocks_pas_a_pas_negoce.pdf"), "votre_declaration_de_stocks_pas_a_pas_negoce.pdf", 'application/pdf');
            $message->attach($attachment);

            $message->setSubject("Déclaration de Stocks \"Négoce\" au 31 juillet ".$campagne)
                    ->setBody("Bonjour,

Vous recevrez dans les prochains jours votre Déclaration de Stocks au 31 juillet ".$campagne." à retourner au CIVA au plus tard le 10 septembre.

Depuis 2014 vous avez la possibilité de télé-déclarer sur le Portail CIVA <https://declaration.vinsalsace.pro>, votre Stock au 31 Juillet voire celui au 31 Décembre si vous êtes concerné.

Le télé-service \"Alsace Stocks\" sera accessible du 18 juillet au 10 septembre inclus, et vous n'aurez donc pas à renvoyer le formulaire papier au CIVA.

Pour vous aider dans votre démarche vous trouverez ci-joint, un document explicatif \"Pas à pas\", vous pourrez également télécharger la Notice d'Aide au format PDF ou consulter l'aide en ligne.


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
            echo 'ERROR;'.$tiers->_id.';ABSENCE DE LIEUX DE STOCKAGE] '.$e->getMessage()."\n";
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
