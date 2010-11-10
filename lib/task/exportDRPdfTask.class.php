<?php

class exportDRPdfTask extends sfBaseTask {

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
            new sfCommandOption('valid', null, sfCommandOption::PARAMETER_REQUIRED, 'campagne must be valid', true),
            new sfCommandOption('cvi', null, sfCommandOption::PARAMETER_REQUIRED, 'export pdf only for a given cvi (put "all" fo all cvi)', 'all'),
            new sfCommandOption('campagne', null, sfCommandOption::PARAMETER_REQUIRED, 'export cvi only for a campagne (put "all" fo all campagnes)', '2010'),
        ));

        $this->namespace = 'export';
        $this->name = 'DRPdf';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [exportDRPdf|INFO] task does things.
Call it with:

  [php symfony exportDRPdf|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        sfContext::createInstance($this->configuration);

        $dr_ids = array();

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

        $file_dir = sfConfig::get('sf_data_dir') . '/export/dr/pdf/';
        if (!file_exists($file_dir)) {
            mkdir(sfConfig::get('sf_data_dir') . '/export/');
            mkdir(sfConfig::get('sf_data_dir') . '/export/dr/');
            mkdir(sfConfig::get('sf_data_dir') . '/export/dr/pdf/');
            $this->logSection($file_dir, 'folder created');
        }

        foreach ($dr_ids as $id) {
            $dr = sfCouchdbManager::getClient("DR")->retrieveDocumentById($id);
            try {
                if (!$dr->updated)
                    throw new Exception();
            } catch (Exception $e) {
                $dr->update();
                $dr->save();
            }
            $tiers = sfCouchdbManager::getClient("Tiers")->retrieveByCvi($dr->cvi);
            if (!$options['valid'] || $dr->isValideeTiers()) {
                $document = new DocumentDR($dr, $tiers, array($this, 'getPartial'), 'pdf', $file_dir, false);
                $document->generatePDF();
                $this->logSection($dr->_id, 'pdf generated');
            }
        }

        $this->logSection("done", $file_dir);
    }

    public function getPartial($templateName, $vars = null) {
        $this->configuration->loadHelpers('Partial');
        $vars = null !== $vars ? $vars : $this->varHolder->getAll();
        return get_partial($templateName, $vars);
    }

}
