<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class exportDSXMLCivaTask
 * @author mathurin
 */
class exportDSXMLCivaTask extends sfBaseTask
{

    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('periode', sfCommandArgument::REQUIRED, 'periode'),
            new sfCommandArgument('folderPath', sfCommandArgument::REQUIRED, 'folderPath'),
            new sfCommandArgument('date', sfCommandArgument::REQUIRED, 'date')
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'export';
        $this->name = 'ds-xml-civa';
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
        $date = $arguments['date'];
        $exportXmlManager = new ExportDSCiva($periode, array(DSCivaClient::TYPE_DS_PROPRIETE));
        $xml = $exportXmlManager->exportXml();
        
        $folderPath = $arguments['folderPath'];
        file_put_contents($folderPath.'/dsXML'.$periode.'_'.$date.".xml", $xml);        
        echo "EXPORT Xml fini\n";
    }
}