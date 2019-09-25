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
        		

        	new sfCommandOption('start', null, sfCommandOption::PARAMETER_OPTIONAL, 'Start date'),
            new sfCommandOption('end', null, sfCommandOption::PARAMETER_OPTIONAL, 'End date'),
            new sfCommandOption('mercuriale', null, sfCommandOption::PARAMETER_OPTIONAL, 'Mercuriale'),
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
        $start = ($options['start'])? str_replace('-', '', $options['start']) : null;
        $end = ($options['end'])? str_replace('-', '', $options['end']) : null;
        $type = ($options['mercuriale'])? $options['mercuriale'] : null;
        
        $prefix = $folderPath.'/'.date('Ymd');
        

        $routing = clone ProjectConfiguration::getAppRouting();
        $contextInstance = sfContext::createInstance($this->configuration);
        $contextInstance->set('routing', $routing);
        
        $vracMercuriale = new VracMercuriale($folderPath, $start, $end, $type);
        $vracMercuriale->setContext($contextInstance);
        
        $vracMercuriale->generateMercurialePlotFiles(array('GW','RI','SY'));
        $vracMercuriale->generateMercurialePlotFiles(array('PN','PG','PB'));
        
        unlink('/tmp/mercuriales/20190201_20190215_mercuriales.pdf');
        $pdf = new ExportVracMercurialePdf($vracMercuriale);
        $pdf->generatePDF();
        
        echo sprintf("Les mercuriales des transactions vrac Alsace AOC ont été générées dans %s\n", $folderPath);
    }
    
}
