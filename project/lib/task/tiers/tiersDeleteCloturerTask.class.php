<?php

class tiersDeleteCloturerTask extends sfBaseTask
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
    ));

    $this->namespace        = 'tiers';
    $this->name             = 'delete-cloturer';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [tiers:ldap-update|INFO] task does things.
Call it with:

  [php symfony tiers:ldap-update|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    ini_set('memory_limit', '512M');

    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $ids = acCouchdbManager::getClient('Tiers')->getAllIds();

    $csv_values = array();
    $csv_values[] = array("N° TIERS", "ID", "N° CVI", "N° CIVABA");
    
    $nb = 0;
    foreach($ids as $id) {
        if ($id != 'TIERS-7523700100') {
            $tiers_json = acCouchdbManager::getClient("Tiers")->find($id, acCouchdbClient::HYDRATE_JSON);
            if (!isset($tiers_json->import_db2_date)) {
                $tiers = acCouchdbManager::getClient('Tiers')->find($id);
                $this->logSection('delete', $id);
                $csv_values[] = array($tiers->num, $tiers->get('_id'), $tiers->cvi, $tiers->civaba);
                $nb++;
            }
        }
    }

    $this->logSection("done", $nb);

    $content_csv = Tools::getCsvFromArray($csv_values);
    $filedir = sfConfig::get('sf_web_dir').'/';
    $filename = 'TIERS-DELETE.csv';
    file_put_contents($filedir.$filename, $content_csv);
    $this->logSection("created", $filedir.$filename);

    // add your code here
  }
}
