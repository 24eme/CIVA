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
        return;
    }

    $mentions = array();

    foreach($dr->recolte->getProduitsDetails() as $detail) {
        $mentionKey = $detail->getCepage()->getMention()->getKey();
        if($detail->getCepage()->getMention()->getKey() == "mention") {
            $mentionKey = $detail->getCepage()->getAppellation()->getKey();
        }

        if(!isset($mentions[$mentionKey])) {
            $mentions[$mentionKey] = array("negoces" => array(), "cooperatives" => array(), "mouts" => array(), "cave_particuliere" => null);
        }

        if($detail->cave_particuliere) {
            $mentions[$mentionKey]["cave_particuliere"] = 1;
        }
        foreach($mentions[$mentionKey] as $keyAcheteur => $acheteur) {
            if(!is_array($acheteur) || !$detail->exist($keyAcheteur)) {
                continue;
            }
            foreach($detail->get($keyAcheteur) as $achat) {
                if(!$achat->quantite_vendue) {
                    continue;
                }
                $mentions[$mentionKey][$keyAcheteur][] = $achat->cvi;
                $mentions[$mentionKey][$keyAcheteur] = array_unique($mentions[$mentionKey][$keyAcheteur]);
            }
        }

        if($mentionKey == "mentionVT" || $mentionKey == "mentionSGN") {
            $mentions[$detail->getCepage()->getAppellation()->getKey()] = $mentions[$mentionKey];
        }
    }

    $modif = false;
    foreach($mentions as $mentionKey => $acheteur) {
        if(!$dr->acheteurs->certification->genre->exist($mentionKey)) {
            $dr->acheteurs->certification->genre->add($mentionKey, $acheteur);
            $modif = true;
            print_r($acheteur);
            echo $mentionKey.";".$arguments['doc_id']."\n";
        }
    }

    if($modif) {
        $dr->save();
    }

  }
}
