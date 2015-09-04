<?php

class DSSendMailRappelTask extends sfBaseTask
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
        $this->name = 'send-mail-rappel';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [ds:send-brouillons] task envoie du mail de rappel
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

        $dsCurrent = $tiers->getDs(CurrentClient::getCurrent()->ds_periode."");
        $ds = $tiers->getDs($this->periode);
        $teledeclarant = ($ds && (!$ds->exist("date_depot_mairie") || !$ds->get("date_depot_mairie")));
        $enCours = ($dsCurrent && !$dsCurrent->isValideeTiers());

        if($dsCurrent && $dsCurrent->isValideeTiers()) {

            return;
        }

        if(!$teledeclarant && !$enCours) {

            return;
        }

        $email = $compte->email;

        $log = sprintf("%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s", $type_ds, $teledeclarant, $enCours, !is_null($ds), $compte->login, $compte->email, $tiers->cvi, $tiers->civaba, $tiers->categorie, $tiers->qualite_categorie, $tiers->nom, $tiers->siege->commune, $compte->_id, $tiers->_id);

        if($dryrun) {
            echo $log.";0\n";
            return;
        }

        $message = Swift_Message::newInstance()
                ->setFrom(array('dominique@civa.fr' => "Dominique Wolff"))
                ->setTo($email);

        $this->configureMessage($message, $teledeclarant, $enCours);

        try {
            $this->getMailer()->send($message);
        } catch (Exception $e) {
            echo $log.";0;".$e->getMessage()."\n";
            return false;
        }

        echo $log.";1\n";

        sleep(1);
    }

    protected function configureMessage($message, $teledeclarant, $enCours) {

        if($enCours) {

            $message->setSubject("RAPPEL DS au 31 juillet 2015")
                    ->setBody("Bonjour,

Vous avez commencé à saisir en ligne votre Déclaration de Stocks 2015 sur le site VinsAlsace.pro, mais ne l'avez pas encore validée.

Nous vous rappelons que vous devez impérativement la valider AVANT le 10 septembre minuit.

Pour terminer la saisie, cliquez sur le lien suivant : <https://declaration.vinsalsace.pro>

Si vous avez souscrit une déclaration papier, merci de m'en informer par retour de mail.


Cordialement, 

Dominique WOLFF");

            return $message;
        }

        if(!$enCours) {

            $message->setSubject("RAPPEL DS au 31 juillet 2015")
                    ->setBody("Bonjour,

En 2014 vous avez télé-déclaré votre Stock sur le Portail du CIVA.

A ce jour nous n'avons pas enregistré de saisie pour celle au 31 Juillet 2015.

Nous vous rappelons que vous devez impérativement déposer ou télé-déclarer votre Stock AVANT le 10 septembre minuit.

Vous pouvez effectuer la saisie en cliquant sur le lien suivant <https://declaration.vinsalsace.pro> 

Si vous avez souscrit une déclaration papier, merci de m'en informer par retour de mail.


Cordialement, 

Dominique WOLFF");

            return $message;
        }
    }
    
    public function getPartial($templateName, $vars = null) {
        $this->configuration->loadHelpers('Partial');
        $vars = null !== $vars ? $vars : $this->varHolder->getAll();
        return get_partial($templateName, $vars);
    }
}
