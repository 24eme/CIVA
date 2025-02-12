<?php

class exportDRPdfTask extends sfBaseTask {

    protected function configure() {
        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            new sfCommandOption('clean', null, sfCommandOption::PARAMETER_REQUIRED, 'Clean All before', false),
            new sfCommandOption('debug', null, sfCommandOption::PARAMETER_REQUIRED, 'Debug', false),
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
        ini_set('memory_limit', '1800M');

        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        sfContext::createInstance($this->configuration);

        $ids = ExportClient::getInstance()->findAll(acCouchdbClient::HYDRATE_JSON)->getIds();

        foreach($ids as $id) {
            $export = acCouchdbManager::getClient()->find($id);
            $this->logSection($export->get('_id'), 'exporting ...');
            $export_dr = new ExportDR($export, array($this, 'getPartial'), $options['debug']);
            $export_dr->export();
            if($options['clean']) {
                $export_dr->clean();
            }
            $publicationPdf = !preg_match('/^EXPORT-MAIRIES-/', $export->_id);
            if($publicationPdf) {
                $export_dr->publication();
            }
            $export_dr->exportStatsCSV();
            if($publicationPdf) {
                $export_dr->zip();
            }
            $export_dr->createHashMd5File();
        }
    }

    public function getPartial($templateName, $vars = null) {
        $this->configuration->loadHelpers('Partial');
        $vars = null !== $vars ? $vars : $this->varHolder->getAll();
        return get_partial($templateName, $vars);
    }
}
