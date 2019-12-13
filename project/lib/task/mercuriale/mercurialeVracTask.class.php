<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class mercurialeVracTask
 */
class mercurialeVracTask extends sfBaseTask
{
	
    protected function configure()
    {
        $this->addArguments(array(
            new sfCommandArgument('folderPath', sfCommandArgument::REQUIRED, 'folderPath'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'mercuriale';
        $this->name = 'vrac';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [mercurialeVrac|INFO] task does things.
Call it with:

  [php symfony mercuriale:vrac|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        set_time_limit(0);
        $folderPath = $arguments['folderPath'];
        $csvFile = $folderPath.VracMercuriale::CSV_FILE_NAME;
        if (file_exists($csvFile)) {
            unlink($csvFile);
        }
        $vracMercuriale = new VracMercuriale($folderPath);
        echo sprintf("Les données pour générer les mercuriales des transactions vrac Alsace AOC ont été générées dans %s\n", $folderPath);
    }
    
}
