<?php

class DRChangeUtilisateurCompteTask extends sfBaseTask
{

    protected function configure()
    {
        $this->addArguments(array(
          new sfCommandArgument('id', sfCommandArgument::REQUIRED, 'DR ID'),
          new sfCommandArgument('compte_id', sfCommandArgument::REQUIRED, 'Compte ID to change'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'dr';
        $this->name = 'changeUtilisateurCompte';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [maintenance:DRVT|INFO] task does things.
Call it with:

  [php symfony maintenance:DRVT|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {

      // initialize the database connection
      $databaseManager = new sfDatabaseManager($this->configuration);
      $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
      
      $dr = DRClient::getInstance()->find($arguments['id']);

      if(!$dr) {
        echo "ERREUR;".$dr->_id.";La DR n'existe pas\n";
        return;
      }

      if(!$dr->isValideeCiva()) {

        return;
      }

      if($dr->hasDateDepotMairie()) {

        return;
      }

      $change = false;

      $edition = array();
      $first = true;
      foreach($dr->utilisateurs->edition as $editeur => $date) {
        if($editeur == "csv") {
          $edition[$editeur] = $date;
          continue;
        }

        if($first && $editeur == $arguments["compte_id"]) {
          $change = true;
          echo "INFO;".$dr->_id.";Editeur : COMPTE-".$dr->cvi." ajouté\n";
          $edition["COMPTE-".$dr->cvi] = $date;
        }

        $first = false;

        $edition[$editeur] = $date;
      }

      $dr->utilisateurs->remove("edition");
      $dr->utilisateurs->add('edition', $edition);

      $validation = array();
      $first = true;
      foreach($dr->utilisateurs->validation as $validateur => $date) {
        if($validateur == "csv") {
          $validation[$validateur] = $date;
          continue;
        }

        if($first && $validateur == $arguments["compte_id"]) {
          $change = true;
          echo "INFO;".$dr->_id.";Validateur : COMPTE-".$dr->cvi." ajouté\n";
          $validation["COMPTE-".$dr->cvi] = $date;
        }

        $first = false;

        $validation[$validateur] = $date;
      }

      $dr->utilisateurs->remove("validation");
      $dr->utilisateurs->add('validation', $validation);
      
      if($change) {
        $dr->save();
        echo "INFO;".$dr->_id.";saved\n";
      }
    }
    
}

