<?php
class acVinCompteAddDroitTask extends sfBaseTask
{
    protected function configure()
    {
        $this->addArguments(array(
            new sfCommandArgument('compte_id', sfCommandArgument::REQUIRED, 'ID du document de compte'),
            new sfCommandArgument('droit', sfCommandArgument::REQUIRED, 'Droit à ajouter'),
         ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace        = 'compte';
        $this->name             = 'add-droit';
        $this->briefDescription = '';
        $this->detailedDescription = "";
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $compte = CompteClient::getInstance()->find($arguments['compte_id']);
        $droit = $arguments['droit'];

        if(!$compte) {
            return;
        }

        $droitsAdded = false;
        if(!$compte->exist('droits') || !count($compte->_get('droits'))) {
            $compte->add('droits', $compte->getDroits());
            $droitsAdded = true;
        }

        if($compte->hasDroit($droit) && !$droitsAdded) {
            return;
        }

        if(!$compte->hasDroit($droit)) {
            $compte->droits->add(null, $droit);
        }

        $compte->save();

        echo $compte->_id.";".$droit.";ajouté\n";
    }
}
