<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class DRSendMailsTask
 * @author mathurin
 */
class DRSendMailsTask extends sfBaseTask
{

    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'dr';
        $this->name = 'send-mails';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [dr:send-mails|INFO] task does things.
Call it with:

  [php symfony dr:send-mails|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        sfContext::createInstance($this->configuration);
        
        $drs_to_send_mail = DRAttenteEnvoiMailView::getInstance()->findAll($arguments['campagne']);
        foreach ($drs_to_send_mail as $dr_result) {
            $dr = acCouchdbManager::getClient("DR")->find($dr_result->id);   
            $tiers = acCouchdbManager::getClient("Recoltant")->retrieveByCvi($dr->cvi);
            $annee = $dr->campagne;
            $this->mailerManager = new RecolteMailingManager($this->getMailer(),array($this, 'getPartial'),$dr,$tiers,$annee); 
            
            $dr_new = acCouchdbManager::getClient("DR")->find($dr_result->id); 

            if($dr->_rev != $dr_new->_rev) {
                continue;
            }

            if(!($dr->exist('en_attente_envoi') && $dr->en_attente_envoi)) {
                continue;
            }

            try {
                $this->mailerManager->sendMail(false);
                if($this->hasAutorisation(DRClient::AUTORISATION_ACHETEURS)) {
                    $this->mailerManager->sendAcheteursMails();
                }
                echo $dr->_id.":Email envoyé à ".$tiers->getCompteEmail()."\n";
            } catch(Exception $e) {
                echo $dr->_id.":".$e->getMessage()."\n";
                continue;
            }

            $dr->emailSended();
            $dr->save();
        }
    }
    
    public function getPartial($templateName, $vars = null) {
        $this->configuration->loadHelpers('Partial');
        $vars = null !== $vars ? $vars : $this->varHolder->getAll();
        return get_partial($templateName, $vars);
    }
    
}