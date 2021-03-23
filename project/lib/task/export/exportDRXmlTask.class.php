<?php

class exportDRXmlTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    $this->addArguments(array(
       new sfCommandArgument('campagne', sfCommandArgument::REQUIRED, 'Campagne'),
       new sfCommandArgument('destinataire', sfCommandArgument::REQUIRED, 'Destinataire'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      new sfCommandOption('docid', null, sfCommandOption::PARAMETER_REQUIRED, 'one document id', ''),
      // add your own options here
      new sfCommandOption('id', null, sfCommandOption::PARAMETER_OPTIONAL, 'Limite la génération à une DR'),
      new sfCommandOption('check', null, sfCommandOption::PARAMETER_OPTIONAL, 'Vérification de cohérence comme celles effectuées par les douanes'),
    ));

    $this->namespace        = 'export';
    $this->name             = 'dr-xml';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [exportDRXml|INFO] task does things.
Call it with:

  [php symfony exportDRXml|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {

    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    @sfContext::createInstance($this->configuration);

    if (!in_array($arguments['destinataire'], array("Civa", "Douane"))) {
        throw new sfCommandException("Le destinataire est invalide !");
    }

    if($options['id']) {
        $dr = acCouchdbManager::getClient("DR")->find($options['id']);
        $xml = new ExportDRXml($dr, array($this, 'getPartial'), $arguments['destinataire']);
        echo $xml->getContent();
        if(count($xml->getErreurs()) > 0 && isset($options['check'])) {
            echo "--------------------------------------------------------------------\n";
            echo "Erreurs :\n";
            foreach($xml->getErreurs() as $erreur) {
                echo "- ".$erreur['col']['L1'].":".$erreur['message']."\n";
            }
        }
        return;
    }

    ini_set('memory_limit', '2500M');

    if(!isset($options['check'])) {
        $filename = $this->getFileDir().'DR-'.$arguments['campagne'].'-'.$arguments['destinataire'].'.xml';
        if (file_exists($filename)) {
            unlink($filename);
        }
        file_put_contents($filename, "<?xml version='1.0' encoding='utf-8' ?>\n<listeDecRec>\n", FILE_APPEND);
    } else {
        echo "ID;Code produit;Erreur Douane\n";
    }
    $nb_exported = 0;

    if ($options['docid']) {
        $dr_ids = array($options['docid']);
    }else{
        $dr_ids = acCouchdbManager::getClient("DR")->getAllByCampagne($arguments['campagne'], acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
    }
    
    $stats = array();

    foreach ($dr_ids as $id) {

            if (!preg_match("/^DR-(67|68)/", $id)) {

                continue;
            }

            $dr = acCouchdbManager::getClient("DR")->find($id);

            if (!$dr->isValideeTiers()) {
                
                continue;
            }

            if (count($dr->recolte->getAppellations()) == 0 && $arguments['destinataire'] == "Civa") {
              $this->logSection($dr->_id, 'DR Vide');
            }

            if (count($dr->recolte->getAppellations()) == 0 && $arguments['destinataire'] != "Civa") {
                continue;
            }

            $xml = new ExportDRXml($dr, array($this, 'getPartial'), $arguments['destinataire']);
            if(isset($options['check'])) {
                foreach($xml->getErreurs() as $erreur) {
                    echo $dr->_id.";".$erreur['col']['L1'].";".$erreur['message']."\n";
                }
                continue;
            }
            file_put_contents($filename, $xml->getContent(), FILE_APPEND);
            $this->logSection($dr->_id, 'xml generated');
            $nb_exported++;
            if($nb_exported > 100) {
                break;
            }
            foreach($xml->getXml() as $col) {
                $key = $col['L1'];
                if(!isset($stats[$key])) {
                    $stats[$key] = array_fill(0,18,0); 
                    $stats[$key][0] = $key;
                }
                
                if(isset($col['L4'])) {
                    $stats[$key][1] += round($col['L4'], 2);
                }
                
                for($i = 5; $i <= 19; $i++) {
                    if(isset($col['exploitant']['L'.$i])) {
                        $stats[$key][$i-3] += round($col['exploitant']['L'.$i], 2);
                    }    
                }
                
                foreach($col['exploitant'] as $k => $values) {
                    if(preg_match('/^L6/', $k)) {
                        $stats[$key][3] += round($values['volume'], 2);
                    }
                    if(preg_match('/^L7/', $k)) {
                        $stats[$key][4] += round($values['volume'], 2);
                    }
                    if(preg_match('/^L8/', $k)) {
                        $stats[$key][5] += round($values['volume'], 2);
                    }
                }
            }
    }
    

    if(isset($options['check'])) {
        return;
    }
    $this->logSection("nb exported", $nb_exported);

    file_put_contents($filename, '</listeDecRec>', FILE_APPEND);
    $this->logSection("done", $filename);
    
    $statFile = $this->getFileDir().'DR-'.$arguments['campagne'].'-'.$arguments['destinataire'].'_stats.csv';
    $fp = fopen($statFile, 'w');
    
    fputcsv($fp, array('L1', 'L4', 'L5', 'L6', 'L7', 'L8', 'L9', 'L10' ,'L11' ,'L12' ,'L13' ,'L14' ,'L15' ,'L16', 'L17', 'L17', 'L18', 'L19'), ';');
    foreach ($stats as $data) {
        fputcsv($fp, $data, ';');
    }

    fclose($fp);
    $this->logSection("stats", $statFile);
  }

  protected function getFileDir() {
        $file_dir = sfConfig::get('sf_data_dir') . '/export/dr/xml/';
        if (!file_exists($file_dir)) {
            mkdir(sfConfig::get('sf_data_dir') . '/export/');
            mkdir(sfConfig::get('sf_data_dir') . '/export/dr/');
            mkdir(sfConfig::get('sf_data_dir') . '/export/dr/xml/');
            $this->logSection($file_dir, 'folder created');
        }
        return $file_dir;
    }

  public function getPartial($templateName, $vars = null) {
        $this->configuration->loadHelpers('Partial');
        $vars = null !== $vars ? $vars : $this->varHolder->getAll();
        return get_partial($templateName, $vars);
  }
}
