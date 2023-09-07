<?php

class DSSendMailRappelTask extends sfBaseTask
{

    protected $debug = false;
    protected $periode = null;

    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('periode', sfCommandArgument::REQUIRED, "Période"),
            new sfCommandArgument('identifiant', sfCommandArgument::REQUIRED, "Identifiant de l'établissement"),
            new sfCommandArgument('type_ds', sfCommandArgument::REQUIRED, 'Type de la DS'),
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

        $etablissement = EtablissementClient::getInstance()->find($arguments['identifiant']);

        if(!$etablissement) {
            $etablissement = EtablissementClient::getInstance()->findByIdentifiant($arguments['identifiant']);
        }

        if(!$etablissement) {
            $etablissement = EtablissementClient::getInstance()->findByCvi($arguments['identifiant']);
        }

        if(!$etablissement) {
            echo "Établissement non trouvé : ".$arguments['identifiant']."\n";
            return;
        }

        if(!preg_match("/^[C]*(67|68)/", $etablissement->identifiant)) {
            return;
        }
        
        $drm = acCouchdbManager::getClient()->find("DRM-".$etablissement->identifiant."-".$arguments['periode'], acCouchdbClient::HYDRATE_JSON);
        
        if($etablissement->isActif() && $etablissement->hasDroit('teledeclaration_ds_'.$arguments['type_ds']) && !$drm) {
            
            echo $etablissement->_id.";Cette établissement à le droit DS mais pas de DRM en juillet\n";
        }   
        
        if(!$drm) {
            return;
        }

        if($arguments['type_ds'] == DSCivaClient::TYPE_DS_NEGOCE && $drm && strpos($drm->declarant->famille, "NEGO") === false) {

            return;
        }
        
        if($arguments['type_ds'] == DSCivaClient::TYPE_DS_PROPRIETE && $drm && strpos($drm->declarant->famille, "NEGO") !== false) {
            
            return;
        }
        
        $ds = DSCivaClient::getInstance()->findPrincipaleByEtablissementAndPeriode($arguments['type_ds'], $etablissement, $arguments['periode']);

        if(!$ds) {
            $this->sendMailOubli($etablissement, $options['dryrun']);
            return;
        }

        if($ds->exist("date_depot_mairie") && $ds->get("date_depot_mairie")) {
            echo "Il s'agit d'une DS papier : ".$ds->_id."\n";
            return;
        }

        if($ds->isValideeTiers()) {
            //echo "La DS est déjà validée : ".$ds->_id."\n";
            return;
        }

        $this->sendMailValidation($etablissement, $ds, $options['dryrun']);
    }

    public function sendMailValidation($etablissement, $ds, $dryrun) {
        $email = $ds->declarant->get('email');
        $message = Swift_Message::newInstance()
            ->setFrom(array(sfConfig::get('app_email_from') => sfConfig::get('app_email_from_name')))
            ->setReplyTo(sfConfig::get('app_email_reply_to'))
            ->setTo($email)
            ->setSubject("RAPPEL DS au 31 juillet ".date('Y'))
            ->setBody("Bonjour,

Vous avez commencé à saisir en ligne votre Déclaration de Stocks ".date('Y')." sur le site VinsAlsace.pro, mais ne l'avez pas encore validée.

Nous vous rappelons que vous devez impérativement la valider AVANT le 10 septembre MINUIT.

Pour terminer la saisie, cliquez sur le lien suivant : <https://declaration.vinsalsace.pro>


Cordialement,

Dominique WOLFF");

        try {
            if(!$email) {
                throw new sfException("Pas de mail");
            }
            if($dryrun) {
                throw new sfException("Dry run");
            }
            $this->getMailer()->send($message);
        } catch (Exception $e) {
            echo "L'envoi du mail validation a échoué (".$email."): ".$e->getMessage()." (".$ds->_id.")\n";
            return false;
        }

        echo "Mail validation envoyé (".$email.") : ".$ds->_id."\n";
        sleep(1);
    }

    public function sendMailOubli($etablissement, $dryrun) {
        $email = $etablissement->getEmailTeledeclaration();
        $message = Swift_Message::newInstance()
        ->setFrom(array(sfConfig::get('app_email_from') => sfConfig::get('app_email_from_name')))
        ->setReplyTo(sfConfig::get('app_email_reply_to'))
        ->setTo($email)
        ->setSubject("RAPPEL DS au 31 juillet ".date('Y'))
        ->setBody("Bonjour,

La Déclaration de Stocks \"Papier\" a définitivement disparue depuis 2017, vous devez donc désormais télé-déclarer votre Stock sur le Portail du CIVA.

A ce jour nous n'avons pas enregistré de saisie pour la déclaration au 31 juillet ".date('Y').".

Nous vous rappelons que vous devez impérativement télé-déclarer votre Stock AVANT le 10 septembre MINUIT.

ATTENTION: Si votre Stock est NEANT, un arrêté du 18 Juillet dernier vous exempte de déposer une Déclaration de Stocks.
Dans ce cas-là, pour éviter qu'on ne vous relance, merci de me le signaler en réponse à ce mail.

Sinon, vous pouvez effectuer la saisie en cliquant sur le lien suivant <https://declaration.vinsalsace.pro>


Cordialement,

Dominique WOLFF
");

        try {
            if(!$email) {
                throw new sfException("Pas de mail");
            }
            if($dryrun) {
                throw new sfException("Dry run");
            }
            $this->getMailer()->send($message);
        } catch (Exception $e) {
            echo "L'envoi du mail oubli a échoué (".$email."): ".$e->getMessage()." (".$etablissement->_id.")\n";
            return false;
        }

        echo "Mail oubli envoyé (".$email.") : ".$etablissement->_id."\n";
        sleep(1);
    }

}
