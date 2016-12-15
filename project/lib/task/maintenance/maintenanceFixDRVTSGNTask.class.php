<?php

class maintenanceFixDRVTSGNTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    $this->addArguments(array(
       new sfCommandArgument('doc_id', sfCommandArgument::REQUIRED, "Document ID"),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      new sfCommandOption('campagne', null, sfCommandOption::PARAMETER_REQUIRED, 'export cvi for a campagne', '2010'),
      // add your own options here
    ));

    $this->namespace        = 'maintenance';
    $this->name             = 'fix-dr-vtsgn';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [maintenanceListDR|INFO] task does things.
Call it with:

  [php symfony maintenanceListDR|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $dr = DRClient::getInstance()->find($arguments['doc_id']);

    if(!$dr) {
        echo "ERROR;Document introuvable;".$arguments['doc_id']."\n";
    }

    $mentions = array();

    foreach($dr->recolte->getProduitsDetails() as $detail) {
        if($detail->getCepage()->getMention()->getKey() == "mention") {
            continue;
        }

        if(!isset($mentions[$detail->getCepage()->getMention()->getKey()])) {
            $mentions[$detail->getCepage()->getMention()->getKey()] = array("negoces" => array(), "cooperatives" => array(), "mouts" => array(), "cave_particuliere" => null);
        }

        if($detail->cave_particuliere) {
            $mentions[$detail->getCepage()->getMention()->getKey()]["cave_particuliere"] = 1;
        }
        foreach($mentions[$detail->getCepage()->getMention()->getKey()] as $keyAcheteur => $acheteur) {
            if(!is_array($acheteur) || !$detail->exist($keyAcheteur)) {
                continue;
            }
            foreach($detail->get($keyAcheteur) as $achat) {
                if(!$achat->quantite_vendue) {
                    continue;
                }
                $mentions[$detail->getCepage()->getMention()->getKey()][$keyAcheteur][] = $achat->cvi;
                array_unique($mentions[$detail->getCepage()->getMention()->getKey()][$keyAcheteur]);
            }
        }
    }

    $modif = false;
    foreach($mentions as $mentionKey => $acheteur) {
        if(!$dr->acheteurs->certification->genre->exist($mentionKey)) {
            $dr->acheteurs->certification->genre->add($mentionKey, $acheteur);
            print_r($acheteur);
            $modif = true;
            echo $mentionKey.";".$arguments['doc_id']."\n";
        }
    }

    if($modif) {
        $dr->save();
    }

  }
}
