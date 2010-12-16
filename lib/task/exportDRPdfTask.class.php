<?php

class exportDRPdfTask extends publierDRAbstractTask {

    protected function configure() {
        // // add your own arguments here
        // $this->addArguments(array(
        //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
        // ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            // add your own options here
            new sfCommandOption('cvi', null, sfCommandOption::PARAMETER_REQUIRED, 'export pdf only for a given cvi (put "all" fo all cvi)', 'all'),
            new sfCommandOption('campagne', null, sfCommandOption::PARAMETER_REQUIRED, 'export cvi only for a campagne (put "all" fo all campagnes)', '2010'),
        ));

        $this->namespace = 'export';
        $this->name = 'dr-pdf';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [exportDRPdf|INFO] task does things.
Call it with:

  [php symfony exportDRPdf|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        ini_set('memory_limit', '700M');

        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        sfContext::createInstance($this->configuration);

        $dr_ids = array();
        $nb_declaration = 0;

        if ($options['cvi'] == 'all' && $options['campagne'] == 'all') {
            $dr_ids = sfCouchdbManager::getClient("DR")->getAll(sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
        } elseif ($options['cvi'] && $options['campagne'] == "all") {
            $dr_ids = array_keys(sfCouchdbManager::getClient("DR")->getArchivesCampagnes($options['cvi'], '2010'));
        } elseif ($options['cvi'] == 'all' && $options['campagne']) {
            $dr_ids = sfCouchdbManager::getClient("DR")->getAllByCampagne($options['campagne'], sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
        } elseif ($options['cvi'] && $options['campagne']) {
            $dr_ids = array(sfCouchdbManager::getClient("DR")->retrieveByCampagneAndCvi($options['cvi'], $options['campagne'])->_id);
        } else {
            new sfCommandException('invalid cvi and campagne');
        }

        $this->createFileDir();
        
        foreach ($dr_ids as $id) {
            $dr = sfCouchdbManager::getClient("DR")->retrieveDocumentById($id);
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
                $tiers = sfCouchdbManager::getClient("Tiers")->retrieveByCvi($dr->cvi);
                if (!$tiers) {
                    $this->logSection("unknow tiers", $dr->_id, null, "ERROR");
                    continue;
                }
                if ($dr->isValideeTiers()) {
                    $filename = $this->getDRFilename($dr, $tiers);
                    $document = new ExportDRPdf($dr, $tiers, array($this, 'getPartial'), 'pdf',  $this->getFileDir(), false, $filename);
                    $cache = $document->isCached();
                    $document->generatePDF();
                    if (!$cache) {
                        $this->logSection($dr->_id, 'pdf generated ('. $this->getFileDir().$filename.')');
                    } else {
                        $this->logSection($dr->_id, 'pdf exist');
                    }
                    $nb_declaration ++;
                    unset($document);
                }
            } catch (Exception $exc) {
                $this->logSection("failed pdf", $dr->_id, null, "ERROR");
                continue;
            }
            unset($dr);
            unset($tiers);
        }

        $this->cleanFile();
        
        $this->logSection('export', $nb_declaration . ' declaration(s)');
    }

    public function getPartial($templateName, $vars = null) {
        $this->configuration->loadHelpers('Partial');
        $vars = null !== $vars ? $vars : $this->varHolder->getAll();
        return get_partial($templateName, $vars);
    }
}
