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
            "type_contrat" => null, // "P" est la seule valeur présente dans le fichier | "P" POUR TOUT 
            "mercuriales" => null, // "C", "I", "M" et "X" : à quoi correspondent ces valeurs ? | NULL POUR TOUT
            "montant_cotisation" => null, // NULL
            "montant_cotisation_paye" => null, // NULL
            "mode_de_paiement" => null, // NULL
            "cvi_acheteur" => null,
            "type_acheteur" => null, // on a l'info mais NULL dans le fichier | VOIR AVEC DOM
            "tca" => null, // ?? | VOIR AVEC DOM
            "cvi_vendeur" => null,
            "type_vendeur" => null, // on a l'info mais NULL dans le fichier | VOIR AVEC DOM
            "numero_contrat" => null,
            "daa" => null, // NULL | NULL POUR TOUT
            "date_arrivee" => null, // ?? | VOIR AVEC DOM
            "date_traitement" => null, // ?? | VOIR AVEC DOM
            "date_saisie" => null,
            "date_circulation" => null, // ?? | VOIR AVEC DOM
            "numero_courtier" => null, // SIREN, numéro carte pro, ... ?? | VOIR AVEC DOM
            "reccod" => null, // ?? | VOIR AVEC DOM
            "total_volume_propose" => null,
            "total_volume_enleve" => null,
            "quantite_transferee" => null, // ?? | VOIR AVEC DOM
            "top_suppression" => null, // ?? (NULL dans le fichier) | VOIR AVEC DOM
            "top_instance" => null, // ?? (NULL et "D" dans le fichier) | VOIR AVEC DOM
            "nombre_contrats" => null, // ?? (0 et 1 dans le fichier) | VOIR AVEC DOM
            "heure_traitement" => null, // ?? | VOIR AVEC DOM
            "utilisateur" => null, // ?? | VOIR AVEC DOM
            "date_modif" => null, // ?? | VOIR AVEC DOM
            );
            
        $modele_ddecvn = array(
            "numero_archive" => null,
            "code_cepage" => null, // ??
            "cepage" => null, // "CH", "CR", "ED" ...
            "code_appellation" => null, // 1, 2, 3
            "numero_ordre" => null, // position produit dans le contrat
            "volume_propose" => null,
            "volume_enleve" => null,
            "prix_unitaire" => null, 
            "degre" => null, // NULL
            "top_mercuriale" => null, // ?? (NULL et "N" dans le fichier) | NULL POUR TOUT
            "millesime" => null, 
            "vtsgn" => null,
            "date_circulation" => null, // première date de retiraison | VOIR AVEC DOM
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