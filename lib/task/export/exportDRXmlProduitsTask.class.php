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

    $csv = new ExportCsv();

    foreach($config->recolte->filter('appellation_') as $appellation) {
      foreach($appellation->getLieux() as $lieu) {
        foreach($lieu->getCouleurs() as $couleur) {
          foreach($couleur->getCepages() as $cepage) {
            try {
              $csv->add(array(
                  "appellation" => $appellation->getLibelle(),
                  "lieu" => $lieu->getLibelle(),
                  "couleur" => $couleur->getLibelle(),
                  "cepage" => $cepage->getLibelle(),
                  "VT/SGN" => "",
                  "code" => $cepage->getDouane()->getFullAppCode(null).$cepage->getDouane()->getCodeCepage(),
              ));
              if ($cepage->hasVtsgn()) {
                $csv->add(array(
                    "appellation" => $appellation->getLibelle(),
                    "lieu" => $lieu->getLibelle(),
                    "couleur" => $couleur->getLibelle(),
                    "cepage" => $cepage->getLibelle(),
                    "VT/SGN" => "VT",
                    "code" => $cepage->getDouane()->getFullAppCode("VT").$cepage->getDouane()->getCodeCepage(),
                ));
                $csv->add(array(
                    "appellation" => $appellation->getLibelle(),
                    "lieu" => $lieu->getLibelle(),
                    "couleur" => $couleur->getLibelle(),
                    "cepage" => $cepage->getLibelle(),
                    "VT/SGN" => "SGN",
                    "code" => $cepage->getDouane()->getFullAppCode("SGN").$cepage->getDouane()->getCodeCepage(),
                ));
              }
            } catch (Exception $e) {
              $this->logSection($appellation->getLibelle() . ' - ' . $lieu->getLibelle() . ' - ' . $cepage->getLibelle(), $e->getMessage());
            }
            
          }
          if ($lieu->hasManyCouleur()) {
            $csv->add(array(
                  "appellation" => $appellation->getLibelle(),
                  "lieu" => $lieu->getLibelle(),
                  "couleur" => $couleur->getLibelle(),
                  "cepage" => "Total",
                  "VT/SGN" => "",
                  "code" => $couleur->getDouane()->getFullAppCode(null),
            ));
          }
      }
      if (!$lieu->hasManyCouleur() && $appellation->hasManyLieu()) {
         $csv->add(array(
                "appellation" => $appellation->getLibelle(),
                "lieu" => $lieu->getLibelle(),
                "couleur" => "",
                "cepage" => "Total",
                "VT/SGN" => "",
                "code" => $lieu->getDouane()->getFullAppCode(null),
        ));
      }
    }
    if (!$appellation->hasManyLieu() && !$appellation->lieu->hasManyCouleur()) {
       $csv->add(array(
              "appellation" => $appellation->getLibelle(),
              "lieu" => "",
              "couleur" => "",
              "cepage" => "Total",
              "VT/SGN" => "",
              "code" => $appellation->getDouane()->getFullAppCode(null),
      ));
    }
  }

    echo $csv->output();
    
  }

}
