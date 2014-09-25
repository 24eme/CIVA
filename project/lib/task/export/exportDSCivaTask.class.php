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
            new sfCommandArgument('periode', sfCommandArgument::REQUIRED, 'periode'),
            new sfCommandArgument('folderPath', sfCommandArgument::REQUIRED, 'folderPath'),
            new sfCommandArgument('type_ds', sfCommandArgument::REQUIRED, 'propriete ou negoce'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default')
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
        $periode = $arguments['periode'];

        if(!in_array($arguments['type_ds'], array(DSCivaClient::TYPE_DS_PROPRIETE, DSCivaClient::TYPE_DS_NEGOCE))) {

            throw new sfException("type ds must be propriete ou negoce");
        }

        $exportManager = new ExportDSCiva($periode, array($arguments['type_ds']));
        $entete = $exportManager->exportEntete();
        $lignes = $exportManager->exportLigne(); 
        
        $folderPath = $arguments['folderPath'];
        $suffixe_type_ds = strtoupper(substr($arguments['type_ds'], 0, 1));
        $path_ent = $folderPath.'/STOENT'.substr($periode, 2, 2).$suffixe_type_ds;
        $path_lig = $folderPath.'/STOLIG'.substr($periode, 2, 2).$suffixe_type_ds;
        
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