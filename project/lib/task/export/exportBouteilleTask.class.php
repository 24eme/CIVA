<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class exportDSCivaTask
 * @author mathurin
 */
class exportBouteilleTask extends sfBaseTask
{

    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('folderPath', sfCommandArgument::REQUIRED, 'folderPath'),
            new sfCommandArgument('date_begin', sfCommandArgument::REQUIRED, 'date'),
            new sfCommandArgument('date_end', sfCommandArgument::OPTIONAL, 'date'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default')
        ));

        $this->namespace = 'export';
        $this->name = 'bouteille';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [exportBouteille|INFO] task does things.
Call it with:

  [php symfony export:vrac|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        set_time_limit(0);
        
        $configCepappctr = new Cepappctr();
        $date_begin = $arguments['date_begin'];
        $date_end = ($arguments['date_end'])? $arguments['date_end'] : date("Y-m-d", mktime(0, 0, 0, date('m'), date('d')-1, date('y'))); 
        $fin = ($arguments['date_end'])? $arguments['date_end'] : date("Y-m-d");
        $dates = array($date_begin, $date_end);
        $filenameHeader = str_replace('-', '', $date_begin).'-'.str_replace('-', '', $fin).'.';

        	$csvBouent = new ExportCsv();
	        $csvBoudet = new ExportCsv();
			$items = VracBouteillesView::getInstance()->findForDb2Export($dates);
			$contrats = array();
			foreach ($items as $item) {
				$contrats[$item->value[VracBouteillesView::VALUE_NUMERO_ARCHIVE]] = $item;
			}
			ksort($contrats);
	        foreach($contrats as $contrat) {
	            $valuesContrat = $contrat->value;
	            $isInCreation = (isset($valuesContrat[VracBouteillesView::VALUE_CREATION]) && $valuesContrat[VracBouteillesView::VALUE_CREATION])? false : true;
	        	if (!$isInCreation) {
	            	continue;
	            }
	            $produits = VracBouteillesProduitsView::getInstance()->findForDb2Export($valuesContrat[VracBouteillesView::VALUE_NUMERO_ARCHIVE]);
	            $i = 0;
	            foreach ($produits as $produit) {
	            	$i++;
	            	$valuesProduit = $produit->value;
	            	$valuesProduit[VracBouteillesProduitsView::VALUE_CODE_APPELLATION] = $this->getCodeAppellation($valuesProduit[VracBouteillesProduitsView::VALUE_CODE_APPELLATION]);
	            	$valuesProduit[VracBouteillesProduitsView::VALUE_CEPAGE] = $this->getCepage($valuesProduit[VracBouteillesProduitsView::VALUE_CEPAGE]);
	            	$valuesProduit[VracBouteillesProduitsView::VALUE_NUMERO_ORDRE] = $i;
	            	$csvBoudet->add($valuesProduit);
	            }
	            $csvBouent->add($valuesContrat);
            	$c = VracClient::getInstance()->find($contrat->id);
            	$c->date_export_creation = date('Y-m-d');
            	$c->forceSave();
	        }
	
	        $bouent = $csvBouent->output();
	        $boudet = $csvBoudet->output();
	        
	        $folderPath = $arguments['folderPath'];
	        $path_bouent = $folderPath.'/'.$filenameHeader.'BOUENT';
	        $path_boudet = $folderPath.'/'.$filenameHeader.'BOUDET';
	        
	        $file_bouent = fopen($path_bouent, 'w');
	        fwrite($file_bouent, "\xef\xbb\xbf");
	        fclose($file_bouent);
	        
	        $file_boudet = fopen($path_boudet, 'w');
	        fwrite($file_boudet, "\xef\xbb\xbf");
	        fclose($file_boudet);
	        
	        file_put_contents($path_boudet, $boudet);        
	        file_put_contents($path_bouent, $bouent);

            
        echo "EXPORT fini\n";
    }
    
    protected function getCodeAppellation($appellation)
    {
    	$code = 1;
    	switch ($appellation) {
                case 'CREMANT':
                    $code = 2;
                    break;
                case 'GRDCRU':
                    $code = 3;
                    break;
                default:
                    $code = 1;
        }
        return $code;
    }
    
    protected function getCepage($cepage)
    {
    	if ($cepage == "BL" || $cepage == "RS") {
    		$cepage = "CR";
    	}
    	return $cepage;
    }
}