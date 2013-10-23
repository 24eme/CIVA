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

    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('date', sfCommandArgument::REQUIRED, 'date'),
            new sfCommandArgument('folderPath', sfCommandArgument::REQUIRED, 'folderPath'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default')
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
        $date = $arguments['date'];
        $dates = array(date('Y-m-d', strtotime("1 day ago" )), $date);
        
        $csvDecven = new ExportCsv();
        $csvDdecvn = new ExportCsv();
		$contrats = VracContratsView::getInstance()->findForDb2Export($dates);
        foreach($contrats as $contrat) {
            $valuesContrat = $contrat->value;
            $produits = VracProduitsView::getInstance()->findForDb2Export($contrat->value[VracContratsView::VALUE_NUMERO_ARCHIVE]);
            $i = 0;
            $dateRetiraison = null;
            foreach ($produits as $produit) {
            	$i++;
            	$valuesProduit = $produit->value;
            	$valuesProduit[VracProduitsView::VALUE_CODE_APPELLATION] = $this->getCodeAppellation($valuesProduit[VracProduitsView::VALUE_CODE_APPELLATION]);
            	$valuesProduit[VracProduitsView::VALUE_NUMERO_ORDRE] = $i;
            	if (!$dateRetiraison || ($valuesProduit[VracProduitsView::VALUE_DATE_CIRCULATION] && $valuesProduit[VracProduitsView::VALUE_DATE_CIRCULATION] < $dateRetiraison)) {
            		$dateRetiraison = $valuesProduit[VracProduitsView::VALUE_DATE_CIRCULATION];
            	}
            	$csvDdecvn->add($valuesProduit);
            }
            $valuesContrat[VracContratsView::VALUE_DATE_CIRCULATION] = $dateRetiraison;
            $csvDecven->add($valuesContrat);
        }

        $decven = $csvDecven->output();
        $ddecvn = $csvDdecvn->output();
        
        $modele_decven = array(
            "numero_archive" => null,
            "type_contrat" => null,
            "mercuriales" => null,
            "montant_cotisation" => null, 
            "montant_cotisation_paye" => null, 
            "mode_de_paiement" => null,
            "cvi_acheteur" => null, // civaba !! ATTENTE VINCENT
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
            "code_cepage" => null, // ordre mercurial "on a pas" !!! attente fichier cepap = couple appellation / cepage
            "cepage" => null,
            "code_appellation" => null,
            "numero_ordre" => null, 
            "volume_propose" => null,
            "volume_enleve" => null,
            "prix_unitaire" => null, 
            "degre" => null,
            "top_mercuriale" => null, // NULL sauf pour le KL appellation 1 (klevener aligenchtagne) ou (appllation 1 et VTSGN non vide 1/2) = "N"
            "millesime" => null,
            "vtsgn" => null,
            "date_circulation" => null,
            );
        
        $folderPath = $arguments['folderPath'];
        $path_ddecvn = $folderPath.'/DDECVN'.'_'.$date;
        $path_decven = $folderPath.'/DECVEN'.'_'.$date;
        
        $file_ddecvn = fopen($path_ddecvn, 'w');
        fwrite($file_ddecvn, "\xef\xbb\xbf");
        fclose($file_ddecvn);
        
        $file_decven = fopen($path_decven, 'w');
        fwrite($file_decven, "\xef\xbb\xbf");
        fclose($file_decven);
        
        file_put_contents($path_ddecvn, $ddecvn);        
        file_put_contents($path_decven, $decven);
        echo "EXPORT fini\n";
    }
    
    protected function getCodeAppellation($appellation)
    {
    	$code = null;
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
}