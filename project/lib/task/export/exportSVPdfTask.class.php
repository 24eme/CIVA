<?php

class exportSVPdfTask extends sfBaseTask {

    protected function configure() {
        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            new sfCommandOption('clean', null, sfCommandOption::PARAMETER_REQUIRED, 'Clean All before', false),
        ));

        $this->namespace = 'export';
        $this->name = 'sv-pdf';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [exportDRPdf|INFO] task does things.
Call it with:

  [php symfony exportSVPdf|INFO]
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
            $export_sv = new ExportSV($export, array($this, 'getPartial'), true);
            $export_sv->export();
            if($options['clean']) {
                $export_sv->clean();
            }
            $publicationPdf = !preg_match('/^EXPORT-MAIRIES-/', $export->_id);
            if($publicationPdf) {
                $export_sv->publication();
            }
            if($publicationPdf) {
                $export_sv->zip();
            }
            $export_sv->createHashMd5File();

        }
    }

    public function getPartial($templateName, $vars = null) {
        $this->configuration->loadHelpers('Partial');
        $vars = null !== $vars ? $vars : $this->varHolder->getAll();
        return get_partial($templateName, $vars);
    }
}
