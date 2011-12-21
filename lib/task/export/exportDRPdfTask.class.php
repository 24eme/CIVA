<?php

class exportDRPdfTask extends sfBaseTask {

    protected function configure() {
        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
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

        $ids = ExportClient::getInstance()->findAll(sfCouchdbClient::HYDRATE_JSON)->getIds();

        foreach($ids as $id) {
            $export = sfCouchdbManager::getClient()->retrieveDocumentById($id);
            $this->logSection($export->get('_id'), 'exporting ...');
            $export_dr = new ExportDR($export, true);
            $export_dr->cleanFolder();
            $export_dr->export(array($this, 'getPartial'));
            $export_dr->createZip();
        }
    }

    public function getPartial($templateName, $vars = null) {
        $this->configuration->loadHelpers('Partial');
        $vars = null !== $vars ? $vars : $this->varHolder->getAll();
        return get_partial($templateName, $vars);
    }
}
