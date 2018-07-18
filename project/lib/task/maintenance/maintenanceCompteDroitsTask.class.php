<?php
class maintenanceCompteDroitsTask extends sfBaseTask
{
    protected function configure()
    {
        $this->addArguments(array(
            new sfCommandArgument('compte_id', sfCommandArgument::REQUIRED, 'ID du document de compte'),
         ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace        = 'maintenance';
        $this->name             = 'compte-droits';
        $this->briefDescription = '';
        $this->detailedDescription = "";
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $compte = CompteClient::getInstance()->find($arguments['compte_id']);

        if(!$compte) {
            return;
        }

        if($compte->isSuspendu()) {
            return;
        }

        if($compte->exist('droits') && count($compte->_get('droits'))) {
            $droitsCompte = $compte->droits->toArray(true, false);
            $droitsSociete = $compte->getSociete()->getDroits();

            sort($droitsCompte);
            sort($droitsSociete);

            if(implode(", ", $droitsCompte) != implode(", ", $droitsSociete)) {
                echo $compte->_id.";Les droits du compte (".implode(", ", $droitsCompte).") ne sont pas les même que ceux de la société (".implode(", ", $droitsSociete).") \n";
            }
            return;
        }

        $compte->add('droits', $compte->getDroits());
        $compte->save();

        echo $compte->_id.";Droits mises à jour\n";
        return;
    }
}
