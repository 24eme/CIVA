<?php

class maintenanceDRCleanDenominationTask extends sfBaseTask
{

    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('id', sfCommandArgument::REQUIRED, 'id'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            new sfCommandOption('devalidation', null, sfCommandOption::PARAMETER_REQUIRED, 'Dévalidation', false),
            new sfCommandOption('try', null, sfCommandOption::PARAMETER_REQUIRED, 'Just try not save', false),
        ));

        $this->namespace = 'maintenance';
        $this->name = 'dr-clean-denomination';
        $this->briefDescription = '';
        $this->detailedDescription = '';
    }

    protected function execute($arguments = array(), $options = array())
    {

        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();


        $dr = DRClient::getInstance()->find($arguments['id']);

        if (!$dr) {
            throw new sfException(sprintf("DR %s introuvable", $arguments['id']));
        }
        
        $rectifier = false;

        foreach($dr->getProduitsDetails() as $detail) {
            if($detail->denomination === "0") {
                $detail->denomination = null;
                echo $dr->_id.";Fixe dénomination ".$detail->getHash()."\n";
                $rectifier = true;
            }
            if($detail->vtsgn === "0") {
                $detail->vtsgn = null;
                echo $dr->_id.";Fixe vtsgn ".$detail->getHash()."\n";
                $rectifier = true;
            }
        }

        if($rectifier) {
            $dr->save(); 
        }
    }

}

