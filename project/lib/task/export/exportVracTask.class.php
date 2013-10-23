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
        $dates = array($date, date('Y-m-d', strtotime("1 day ago" )));
        
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
            "type_contrat" => null, // "P" POUR TOUT 
            "mercuriales" => null, // cf mail du 5/10/2013
            "montant_cotisation" => null, // NULL
            "montant_cotisation_paye" => null, // NULL
            "mode_de_paiement" => null, // NULL
            "cvi_acheteur" => null, // civaba !!
            "type_acheteur" => null, // NULL
            "tca" => null, // NULL
            "cvi_vendeur" => null, // cvi
            "type_vendeur" => null, // NULL
            "numero_contrat" => null, // cf mail du 5/10/2013
            "daa" => null, // NULL
            "date_arrivee" => null, // date creation contrat
            "date_traitement" => null, // date creation contrat
            "date_saisie" => null, // date VALIDATION contrat !!
            "date_circulation" => null, // date validation contrat ou date de premiere retiraison total
            "numero_courtier" => null, // numero tiers 90000...
            "reccod" => null, // NULL
            "total_volume_propose" => null,
            "total_volume_enleve" => null,
            "quantite_transferee" => null, // NULl
            "top_suppression" => null, // NULL
            "top_instance" => null, // NULL
            "nombre_contrats" => null, // NULL
            "heure_traitement" => null, // NULL
            "utilisateur" => null, // cf mail du 5/10/2013 TELEDECL
            "date_modif" => null, // = "date_saisie"
            );
            
        $modele_ddecvn = array(
            "numero_archive" => null, // cf mail du 5/10/2013
            "code_cepage" => null, // ordre mercurial "on a pas" !!! attente fichier cepap = couple appellation / cepage
            "cepage" => null, // "CH", "CR", "ED" ...
            "code_appellation" => null, // 1, 2, 3
            "numero_ordre" => null, // position produit dans le contrat
            "volume_propose" => null,
            "volume_enleve" => null, // volume_enleve = volume_propose a la creation !!
            "prix_unitaire" => null, 
            "degre" => null, // NULL
            "top_mercuriale" => null, // NULL sauf pour le KL appellation 1 (klevener aligenchtagne) ou (appllation 1 et VTSGN non vide 1/2) = "N"
            "millesime" => null, // !! sur deux caractere
            "vtsgn" => null,// 1=vt et 2=sgn
            "date_circulation" => null, // première date de retiraison si creation = date contrat (validation)
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