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
    const FLAG_EXPORT_DB2 = 'CONTRAT_BOUTEILLE_EXPORT_DB2';

    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('folderPath', sfCommandArgument::REQUIRED, 'folderPath'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            new sfCommandOption('date-end', null, sfCommandOption::PARAMETER_OPTIONAL, 'The end date au format yyyy-mm-dd (default : date of day - 1)'),
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
        
        $date_begin = Flag::getFlag(self::FLAG_EXPORT_DB2, date('1990-01-01'));
        $date_end = ($options['date-end'])? $options['date-end'] : date("Y-m-d", mktime(0, 0, 0, date('m'), date('d')-1, date('y'))); 
        $dates = array($date_begin, $date_end);
        $filenameHeader = str_replace('-', '', $date_begin).'-'.str_replace('-', '', $date_end).'.';
        $folderPath = $arguments['folderPath'];

        if($date_begin > $date_end) {
            echo sprintf("Les contrats bouteilles ont déjà été exportés jusqu'au %s\n", $date_end);
            echo sprintf("Aucun export effectué\n", $date_end);
            return;
        }

        $zip = new ZipArchive();
        $zip->open($folderPath.'/'.$filenameHeader.'CONTRATS_BOUTEILLE.zip', ZipArchive::OVERWRITE);

    	$csvBouent = new ExportCsv(null, "\r\n");
        $csvBoudet = new ExportCsv(null, "\r\n");
		$items = VracBouteillesView::getInstance()->findForDb2Export($dates);
		$contrats = array();
        $contrats_to_flag = array();
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
                if($this->getCodeAppellation($produit->value[VracBouteillesProduitsView::VALUE_CODE_APPELLATION]) < 0) {
                    continue;
                }
            	$i++;
            	$valuesProduit = $produit->value;
                unset($valuesProduit[VracBouteillesProduitsView::VALUE_DENOMINATION]);
            	$valuesProduit[VracBouteillesProduitsView::VALUE_CODE_APPELLATION] = $this->getCodeAppellation($valuesProduit[VracBouteillesProduitsView::VALUE_CODE_APPELLATION]);
            	$valuesProduit[VracBouteillesProduitsView::VALUE_CEPAGE] = $this->getCepage($valuesProduit[VracBouteillesProduitsView::VALUE_CEPAGE], $valuesProduit[VracBouteillesProduitsView::VALUE_CODE_APPELLATION]);
            	$valuesProduit[VracBouteillesProduitsView::VALUE_NUMERO_ORDRE] = $i;
            	$csvBoudet->add($valuesProduit);
            }
            $csvBouent->add($valuesContrat);
            $contrats_to_flag[] = $contrat->id;
        }

        $bouent = $csvBouent->output();
        $boudet = $csvBoudet->output();

        $filename_bouent = $filenameHeader.'BOUENT'; 
        $filename_boudet = $filenameHeader.'BOUDET';

        $path_bouent = $folderPath.'/'.$filename_bouent;
        $path_boudet = $folderPath.'/'.$filename_boudet;
        
        file_put_contents($path_boudet, $boudet);        
        file_put_contents($path_bouent, $bouent);

        $zip->addFile($path_bouent, $filename_bouent);
        $zip->addFile($path_boudet, $filename_boudet);

        $zip->close();

        foreach($contrats_to_flag as $id) {
            $c = VracClient::getInstance()->find($id);
            $c->date_export_creation = date('Y-m-d');
            $c->forceSave();
        }

        Flag::setFlag(self::FLAG_EXPORT_DB2, date('Y-m-d'));

        echo sprintf("L'export pour la période du %s au %s est terminé\n", $date_begin, $date_end);
    }
    
    protected function getCodeAppellation($appellation)
    {
        if($appellation == "VINTABLE") {
            
            return -1;
        }

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

    protected function getCepage($cepage, $appellation)
    {
    	if ($appellation == 'CREMANT') {
    		return "CR";
    	}
    	return $cepage;
    }
}
