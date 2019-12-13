<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class emailDSValidationtask
 * @author mathurin
 */
class VracCheckDateTask extends sfBaseTask {

  protected function configure()
  {

    $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            new sfCommandOption('vracid', null, sfCommandOption::PARAMETER_REQUIRED, 'id du contrat', ''),

    ));

    $this->namespace        = 'vrac';
    $this->name             = 'check-date';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [VracUpdateTypeContrat|INFO] task does things.
Call it with:

  [php symfony vrac:update-type-contrat|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    set_time_limit('240');
    ini_set('memory_limit', '512M');

    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    if ($options['vracid']) {
        $contrats = array( $options['vracid']);
    }else{
        $thecontrats = VracTousView::getInstance()->findAll();
        foreach ($thecontrats as $contrat) {
            $contrats[$contrat->id] = $contrat->id;
        }
        $contrats = array_keys($contrats);
    }
    foreach($contrats as $id) {
    	if ($contratObject = VracClient::getInstance()->find($id)) {
          if (($contratObject->valide->date_validation) && ($contratObject->type_contrat == "VRAC") && ($contratObject->valide->statut != "ANNULE")) {
            foreach($contratObject->declaration->getProduitsDetails() as $produit) {
                if ($produit->millesime && $contratObject->valide->date_validation) {
                    if ( $produit->millesime."1201" * 1  > str_replace('-', '', $contratObject->valide->date_validation) * 1) {
                        if (str_replace('-', '', $contratObject->valide->date_validation) * 1 >  $produit->millesime."1101" * 1) {
                            print $contratObject->_id." : réécriture de la date de ".$contratObject->valide->date_validation." pour le 1er décembre ".$produit->millesime."\n";
                            $contratObject->valide->date_validation = $produit->millesime."-12-01";
                            $contratObject->save();
                            break;
                        }else {
                            print "Date non valide pour ".$contratObject->_id." (".$produit->millesime."1201 > ".str_replace('-', '', $contratObject->valide->date_validation).") \n ";
                        }
                    }
                }else{
                    continue;
                    $a = explode('-', $contratObject->valide->date_validation);
                    if ("1201" > $a[1].$a[2] && "0801" < $a[1].$a[2]) {
                        print "Date non valide (sans millesime) pour ".$contratObject->_id." (".$produit->millesime."-12-01 > ".$contratObject->valide->date_validation.") \n ";
                    }
                }
            }
    	}
      }
    }
  }
}
