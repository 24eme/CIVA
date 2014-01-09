<?php

class exportDRXmlProduitsTask extends sfBaseTask
{
  protected function configure()
  {
    // add your own arguments here
    $this->addArguments(array(
      new sfCommandArgument('campagne', sfCommandArgument::REQUIRED, 'Campagne'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
    ));

    $this->namespace        = 'export';
    $this->name             = 'dr-xml-produits';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [exportDRXmlProduits|INFO] task does things.
Call it with:

  [php symfony exportDRXmlProduits|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    ini_set('memory_limit', '2500M');
    
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $config = ConfigurationClient::getConfiguration($arguments['campagne']);

    if (!$config) {
        throw new sfCommandArgumentsException(sprintf("campagne %s does not exist", $arguments['campagne']));
    }

    $produits = $config->getProduitsLibellesByCodeDouane();
    foreach($produits as $code => $libelle) {

      echo sprintf("%s;\"%s\"\n", $libelle, $code);
    }
    
  }

}
