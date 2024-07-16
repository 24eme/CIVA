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

        if(!preg_match("/^C*(67|68)/", $etablissement->identifiant)) {

            return;
        }

        $etablissement = CompteClient::getInstance()->find($arguments["id_etablissement"]);

        if($etablissement->hasDroit(Roles::TELEDECLARATION_DS_PROPRIETE)) {

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


        $previousPeriode = (preg_replace("/[0-9]{2}$/", "", $this->periode)-1).preg_replace("/^[0-9]{4}/", "", $this->periode);
        $ds = DSCivaClient::getInstance()->findPrincipaleByEtablissementAndPeriode($type_ds, $tiers, $previousPeriode);
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
                ->setFrom(array(sfConfig::get('app_email_from') => sfConfig::get('app_email_from_name')))
                ->setReplyTo(sfConfig::get('app_email_reply_to'))
                ->setTo($email);

        $this->configureMessage($message, $type_ds, $teledeclarant);

        if($document && $pdfContent) {
            $attachment = new Swift_Attachment($pdfContent, $document->getFileName(), 'application/pdf');
            $message->attach($attachment);
        }

        //$attachment = new Swift_Attachment(file_get_contents(sfConfig::get('sf_web_dir')."/helpPdf/dai.pdf"), "DAI_2019.pdf",'application/pdf');
        //$message->attach($attachment);

        try {
            $this->getMailer()->send($message);
        } catch (Exception $e) {
            echo $log.";1;0;".$e->getMessage()."\n";
            return false;
        }

        echo $log.";1;1\n";

        sleep(1);
    }

    protected function configureMessage($message, $type_ds, $teledeclarant) {
        $campagne = preg_replace("/[0-9]{2}$/", "", $this->periode);

        if($type_ds == DSCivaClient::TYPE_DS_PROPRIETE) {

            $message->setSubject("Déclaration de Stocks \"Propriété\" au 31 juillet ".$campagne)
                    ->setBody("Bonjour,

Depuis 2017 la souscription de votre Déclaration de Stocks doit obligatoirement se faire par voie électronique.

Comme les années précédentes, vous pourrez accéder à ce télé-service dès le 24 juillet sur le Portail du CIVA <https://login.vinsalsace.pro>.

Attention la date limite de la télé-déclaration est fixée par la Douane au 10 septembre 2024 MINUIT.

Pour vous aider dans votre démarche vous trouverez ci-joint un brouillon personnalisé de votre DS 2024, qui reprend les produits théoriquement détenus en stocks, vous pouvez également télécharger la Notice d'Aide au format PDF ou consulter l'aide en ligne.

Ce document constitue une aide à la télé-déclaration et n'est en aucun cas à retourner au CIVA.

RAPPELS :
    - DEPASSEMENT : depuis la campagne 2019/2020, les volumes en dépassement de rendement doivent être déclarés par couleur dans les rubriques DRA/DPLC blanc et rouge.
    - VCI : si vous détenez encore du VCI, vous devez le déclarer dans l'Appellation correspondante.
    - STOCK NEANT : en cas de Stock Néant, un Arrêté du 18 juillet 2018 exempte les opérateurs vitivinicoles de l'obligation de souscrire une Déclaration de Stock.

IMPORTANT : Clôture de Campagne
    - La DRM de Juillet (à déposer entre le 1er et le 10 Août) sera à établir de façon habituelle et donc à clôturer avec un STOCK THEORIQUE.
    - Les modalités concernant la DRM d'Août, les Pertes et Manquants, ainsi que la DAI (Déclaration Annuelle d'Inventaire) vous seront rapidement précisées par mail.

Cordialement,

Le CIVA");

            return $message;
        }

        if($type_ds == DSCivaClient::TYPE_DS_NEGOCE) {

                $message->setSubject("Déclaration de Stocks \"Négoce\" au 31 juillet ".$campagne)
                    ->setBody("Bonjour,

Depuis 2017 la souscription de votre Déclaration de Stocks doit obligatoirement se faire par voie électronique sur le Portail du CIVA <https://login.vinsalsace.pro>.

Le téléservice \"Alsace Stocks\" sera accessible dès le 24 juillet, et jusqu'au 10 septembre 2024 inclus.

Pour vous aider dans votre démarche vous pourrez télécharger la Notice d'Aide au format PDF
ou consulter l'aide en ligne.

RAPPELS :
    - DEPASSEMENT : depuis la campagne 2019/2020, les volumes en dépassement de rendement doivent être déclarés par couleur dans les rubriques DRA/DPLC blanc et rouge.
    - VCI : si vous détenez encore du VCI, vous devez le déclarer dans l'Appellation correspondante.
    - STOCK NEANT : en cas de Stock Néant, un Arrêté du 18 juillet 2018 exempte les opérateurs vitivinicoles de l'obligation de souscrire une Déclaration de Stock.

IMPORTANT : Clôture de Campagne (pour les opérateurs qui clôturent au 31 juillet)
    - La DRM de Juillet (à déposer entre le 1er et le 10 Août) sera à établir de façon habituelle et donc à clôturer avec un STOCK THEORIQUE
    - Les modalités concernant la DRM d'Août, les Pertes et Manquants, ainsi que la DAI (Déclaration Annuelle d'Inventaire) vous seront rapidement précisées par mail.

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
