<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class exportDSCivaTask
 * @author mathurin
 */
class exportDSCivaTask extends sfBaseTask
{

    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('campagne', sfCommandArgument::REQUIRED, 'campagne'),
            new sfCommandArgument('folderPath', sfCommandArgument::REQUIRED, 'folderPath'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'export';
        $this->name = 'ds-civa';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [exportDSCiva|INFO] task does things.
Call it with:

  [php symfony exportDSCiva|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        set_time_limit(0);
        $campagne = $arguments['campagne'];
        $exportManager = new ExportDSCiva($campagne);
        $entete = $exportManager->exportEntete();
        $lignes = $exportManager->exportLigne(); 
        
        $folderPath = $arguments['folderPath'];
        file_put_contents($folderPath.'/STOENT'.substr($campagne, 2), $entete);        
        file_put_contents($folderPath.'/STOLIG'.substr($campagne, 2), $lignes);
        echo "EXPORT fini\n";
    }
}