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
        $path_ent = $folderPath.'/STOENT'.substr($campagne, 2);
        $path_lig = $folderPath.'/STOLIG'.substr($campagne, 2);
        
        $ent = fopen($path_ent, 'w');
        fwrite($ent, "\xef\xbb\xbf");
        fclose($ent);
        
        $lig = fopen($path_lig, 'w');
        fwrite($lig, "\xef\xbb\xbf");
        fclose($lig);
        
        file_put_contents($path_ent, $entete);        
        file_put_contents($path_lig, $lignes);
        echo "EXPORT fini\n";
    }
}