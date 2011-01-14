<?php

class exportDRXmlTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
      new sfCommandOption('campagne', null, sfCommandOption::PARAMETER_REQUIRED, 'export for a campagne', '2010'),
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

    $filename = $this->getFileDir().'DR-'.$options['campagne'].'.xml';
    if (file_exists($filename)) {
        unlink($filename);
    }
    
    file_put_contents($filename, "<?xml version='1.0' encoding='utf-8' ?>\n<listeDecRec>", FILE_APPEND);

    $dr_ids = sfCouchdbManager::getClient("DR")->getAllByCampagne($options['campagne'], sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
    foreach ($dr_ids as $id) {
        if ($id !== 'DR-7523700100-'.$options['campagne']) {
            $dr = sfCouchdbManager::getClient("DR")->retrieveDocumentById($id);
            /*if ($options['campagne'] == "2010" && $dr->exist('import_db2') && $dr->import_db2 == 1) {
                continue;
            }*/
            try {
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
            }

            try {
                if ($dr->isValideeTiers()) {
                    $xml = new ExportDRXml($dr, array($this, 'getPartial'));
                    file_put_contents($filename, $xml->getContent(), FILE_APPEND);
                    $this->logSection($dr->_id, 'xml generated');
                    unset($xml);
                }
            } catch (Exception $exc) {
                $this->logSection("failed xml", $dr->_id, null, "ERROR");
            }
            unset($dr);
        }
    }
    
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
