<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class exportDSCivaTask
 * @author mathurin
 */
class exportVracTask extends sfBaseTask
{
    const FLAG_EXPORT_DB2 = 'CONTRAT_VRAC_EXPORT_DB2';

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
        $this->name = 'vrac';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [exportVrac|INFO] task does things.
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
        $date_begin = Flag::getFlag(self::FLAG_EXPORT_DB2, date('1990-01-01'));
        $date_end = ($options['date-end'])? $options['date-end'] : date("Y-m-d", mktime(0, 0, 0, date('m'), date('d')-1, date('y'))); 
        $dates = array($date_begin, $date_end);
        $filenameHeader = str_replace('-', '', $date_begin).'-'.str_replace('-', '', $date_end).'.';
        $folderPath = $arguments['folderPath'];

        if($date_begin > $date_end) {
            echo sprintf("Les contrats vracs ont déjà été exportés jusqu'au %s\n", $date_end);
            echo sprintf("Aucun export effectué\n", $date_end);
            return;
        }

        $zip = new ZipArchive();
        $zip->open($folderPath.'/'.$filenameHeader.'CONTRATS_VRAC.zip', ZIPARCHIVE::CREATE | ZipArchive::OVERWRITE);

        /*
         * CREATION
         */
        $types = array('C', 'M');
        $contrats_to_flag = array();
        foreach ($types as $type) {
            $csvDecven = new ExportCsv(null, "\r\n");
            $csvDdecvn = new ExportCsv(null, "\r\n");
            $items = VracContratsView::getInstance()->findForDb2Export($dates, $type);
            $contrats = array();
			foreach ($items as $item) {
				$contrats[$item->value[VracContratsView::VALUE_NUMERO_ARCHIVE]] = $item;
			}
			ksort($contrats);
	        foreach($contrats as $contrat) {
	            $valuesContrat = $contrat->value;
	            $isInCreation = (isset($valuesContrat[VracContratsView::VALUE_CREATION]) && $valuesContrat[VracContratsView::VALUE_CREATION])? true : false;
	            unset($valuesContrat[VracContratsView::VALUE_CREATION]);
            	if ($type == 'C') {
            		$valuesContrat[VracContratsView::VALUE_TOTAL_VOLUME_ENLEVE] = $valuesContrat[VracContratsView::VALUE_TOTAL_VOLUME_PROPOSE];
            	}
	        	if ($type == 'C' && !$isInCreation) {
	            	continue;
	            }
	            $produits = VracProduitsView::getInstance()->findForDb2Export($contrat->value[VracContratsView::VALUE_NUMERO_ARCHIVE]);
	            $i = 0;
	            $dateRetiraison = $valuesContrat[VracContratsView::VALUE_DATE_CIRCULATION];
	            $dateRetiraisonTmp = null;
	            $totalVolEnleve = 0;
	            foreach ($produits as $produit) {
                    if($this->getCodeAppellation($produit->value[VracProduitsView::VALUE_CODE_APPELLATION]) < 0) {
                        continue;
                    }
	            	$i++;
	            	if ($type == 'M' && !$produit->value[VracProduitsView::VALUE_DATE_CIRCULATION]) {
	            		continue;
	            	}
	            	$valuesProduit = $produit->value;
                    unset($valuesProduit[VracProduitsView::VALUE_DENOMINATION]);
                    $valuesProduit[VracProduitsView::VALUE_CEPAGE] = $this->getCepage($valuesProduit[VracProduitsView::VALUE_CEPAGE], $valuesProduit[VracProduitsView::VALUE_CODE_APPELLATION]);
	            	$valuesProduit[VracProduitsView::VALUE_CODE_APPELLATION] = $this->getCodeAppellation($valuesProduit[VracProduitsView::VALUE_CODE_APPELLATION]);
	            	$valuesProduit[VracProduitsView::VALUE_CODE_CEPAGE] = $configCepappctr->getOrdreMercurialeByPair($valuesProduit[VracProduitsView::VALUE_CODE_APPELLATION], $valuesProduit[VracProduitsView::VALUE_CEPAGE]);
	            	$valuesProduit[VracProduitsView::VALUE_NUMERO_ORDRE] = $i;
	            	$valuesProduit[VracProduitsView::VALUE_PRIX_UNITAIRE] = $valuesProduit[VracProduitsView::VALUE_PRIX_UNITAIRE] / 100;
	            	$valuesProduit[VracProduitsView::VALUE_TOP_MERCURIALE] = $this->getTopMercuriale($valuesProduit);
	            	if ($type == 'C') {
	            		$valuesProduit[VracProduitsView::VALUE_VOLUME_ENLEVE] = $valuesProduit[VracProduitsView::VALUE_VOLUME_PROPOSE];
	            		$valuesProduit[VracProduitsView::VALUE_DATE_CIRCULATION] = $valuesContrat[VracContratsView::VALUE_DATE_SAISIE];
	            	}
	            	$totalVolEnleve += $valuesProduit[VracProduitsView::VALUE_VOLUME_ENLEVE];
	            	if (!$dateRetiraisonTmp || ($valuesProduit[VracProduitsView::VALUE_DATE_CIRCULATION] && $valuesProduit[VracProduitsView::VALUE_DATE_CIRCULATION] < $dateRetiraisonTmp)) {
	            		$dateRetiraisonTmp = $valuesProduit[VracProduitsView::VALUE_DATE_CIRCULATION];
	            	}
	            	$csvDdecvn->add($valuesProduit);
	            }
	            if ($type == 'M') {
	            	$valuesContrat[VracContratsView::VALUE_TOTAL_VOLUME_ENLEVE] = $totalVolEnleve;
	            }
	            $valuesContrat[VracContratsView::VALUE_DATE_CIRCULATION] = ($type == 'M' && $dateRetiraisonTmp)? $dateRetiraisonTmp : $dateRetiraison;
	            $csvDecven->add($valuesContrat);
	        	if ($type == 'C') {
                    $contrats_to_flag[] = $contrat->id;
	            }
	        }
	
	        $decven = $csvDecven->output();
	        $ddecvn = $csvDdecvn->output();
	        
            $filename_ddecvn = $filenameHeader.'DDECVN-'.$type; 
            $filename_decven = $filenameHeader.'DECVEN-'.$type; 
	        $path_ddecvn = $folderPath.'/'.$filename_ddecvn;
	        $path_decven = $folderPath.'/'.$filename_decven;
	        
	        file_put_contents($path_ddecvn, $ddecvn);        
	        file_put_contents($path_decven, $decven);

            $zip->addFile($path_ddecvn, $filename_decven);
            $zip->addFile($path_decven, $filename_ddecvn);
        }
            
        $zip->close();
        foreach($contrats_to_flag as $id) {
            $c = VracClient::getInstance()->find($id);
            $c->date_export_creation = date('Y-m-d');
            $c->forceSave();
        }

        Flag::setFlag(self::FLAG_EXPORT_DB2, date('Y-m-d'));
        
        $modele_decven = array(
            "numero_archive" => null,
            "type_contrat" => null,
            "mercuriales" => null,
            "montant_cotisation" => null, 
            "montant_cotisation_paye" => null, 
            "mode_de_paiement" => null,
            "cvi_acheteur" => null,
            "type_acheteur" => null,
            "tca" => null,
            "cvi_vendeur" => null,
            "type_vendeur" => null,
            "numero_contrat" => null,
            "daa" => null,
            "date_arrivee" => null,
            "date_traitement" => null,
            "date_saisie" => null,
            "date_circulation" => null,
            "numero_courtier" => null, // numero tiers 90000...
            "reccod" => null,
            "total_volume_propose" => null,
            "total_volume_enleve" => null,
            "quantite_transferee" => null,
            "top_suppression" => null,
            "top_instance" => null,
            "nombre_contrats" => null,
            "heure_traitement" => null,
            "utilisateur" => null,
            "date_modif" => null,
            );
            
        $modele_ddecvn = array(
            "numero_archive" => null,
            "code_cepage" => null,
            "cepage" => null,
            "code_appellation" => null,
            "numero_ordre" => null, 
            "volume_propose" => null,
            "volume_enleve" => null,
            "prix_unitaire" => null, 
            "degre" => null,
            "top_mercuriale" => null,
            "millesime" => null,
            "vtsgn" => null,
            "date_circulation" => null,
            );
  
        echo sprintf("L'export pour la période du %s au %s est terminé\n", $date_begin, $date_end);
    }
    
    protected function getTopMercuriale ($ligne) 
    {
    	$top_mercuriale = null;
    	if ($ligne[VracProduitsView::VALUE_CODE_APPELLATION] == 1) {
    		if ($ligne[VracProduitsView::VALUE_VTSGN]) {
    			$top_mercuriale = "N";
    		}
    		if ($ligne[VracProduitsView::VALUE_CEPAGE] == "KL") {
    			$top_mercuriale = "N";
    		}
    	}
    	return $top_mercuriale;
    }

    protected function getCepage($appellation) {
    {
        if ($appellation == 'CREMANT') {

            return "CR";
        }

        if ($cepage == "AU" || $cepage == "PI") {
            $cepage = "PB";
        }

        if ($cepage == "MO") {
            $cepage = "MU";
        }

        return $cepage;
    }

    protected function getCodeAppellation($appellation) {
        return VracMercuriale::getCodeAppellation($appellation);
    }



}
