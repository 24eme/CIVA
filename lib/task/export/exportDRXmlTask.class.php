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
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
      
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
    ini_set('memory_limit', '2500M');
    
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    sfContext::createInstance($this->configuration);

    if (!in_array($arguments['destinataire'], array("Civa", "Douane"))) {
        throw new sfCommandException("Le destinataire est invalide !");
    }

    $filename = $this->getFileDir().'DR-'.$arguments['campagne'].'-'.$arguments['destinataire'].'.xml';
    if (file_exists($filename)) {
        unlink($filename);
    }

    $nb_exported = 0;

    /*$drs_id_file = array();
    foreach (file(sfConfig::get('sf_data_dir') . '/Fichier99DRrejetees.csv') as $a) {
        $csv = explode(',', preg_replace('/"/', '', $a));
        if (trim($csv[0])) {
          $drs_id_file[] = 'DR-'.trim($csv[0]).'-'.$arguments['campagne'];
        }
    }
    $this->logSection("dr impacter", count($drs_id_file));
    */

    file_put_contents($filename, "<?xml version='1.0' encoding='utf-8' ?>\n<listeDecRec>", FILE_APPEND);

    $dr_ids = sfCouchdbManager::getClient("DR")->getAllByCampagne($arguments['campagne'], sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
    foreach ($dr_ids as $id) {
        if ($id !== 'DR-7523700100-'.$arguments['campagne'] /*&& in_array($id, $drs_id_file)*/) {
            $dr = sfCouchdbManager::getClient("DR")->retrieveDocumentById($id);
            /*if ($arguments['campagne'] == "2010" && $dr->exist('import_db2') && $dr->import_db2 == 1) {
                continue;
            }*/
            /*try {
                if (!$dr->updated)
                    throw new Exception();
            } catch (Exception $e) {
                try {
                    $dr->update();
                    $dr->save();
                } catch (Exception $exc) {
                    $this->logSection("failed update", $dr->_id, null, "ERROR");
                    continue;
                }
            }*/

            //try {
                if ($dr->isValideeTiers()) {
                    if (count($dr->recolte->getAppellations()) > 0) {
                        $xml = new ExportDRXml($dr, array($this, 'getPartial'), $arguments['destinataire']);
                        file_put_contents($filename, $xml->getContent(), FILE_APPEND);
                        $this->logSection($dr->_id, 'xml generated');
                        $nb_exported++;
                        unset($xml);
                    }
                }
            /*} catch (Exception $e) {
                $this->logSection("failed xml", $dr->_id, null, "ERROR");
                $this->logSection("erreur", $e->getMessage(), null, "ERROR");
            }*/
            unset($dr);

            /*if($nb_exported == 250) {
                break;
            }*/
        }
    }


    $this->logSection("nb exported", $nb_exported);
    
    file_put_contents($filename, '</listeDecRec>', FILE_APPEND);
    $this->logSection("done", $filename);
    
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
