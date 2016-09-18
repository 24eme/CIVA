<?php

class exportConfigurationCsvTask extends sfBaseTask {

    protected function configure() {
        $this->addArguments(array(
            new sfCommandArgument('doc_id', sfCommandArgument::REQUIRED, "ID du document de Configuration"),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
        ));

        $this->namespace = 'export';
        $this->name = 'configuration-csv';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $conf = ConfigurationClient::getInstance()->find($arguments['doc_id']);
        foreach($conf->recolte->getProduits() as $produit) {

            $certificationKey = "AOC_ALSACE";
            $certificationLibelle = "AOC";
            $genreKey = "TRANQ";

            if($produit->getAppellation()->getKey() == "appellation_VINTABLE") {
                $certificationKey = "VINSSIG";
                $certificationLibelle = "Vins sans IG";
            }

            if($produit->getAppellation()->getKey() == "appellation_CREMANT") {
                $genreKey = "EFF";
            }

            if($produit->getLibelleLong() == "Mousseux") {
                $genreKey = "EFF";
            }

            $cotisationDouane = "L387;Vins Tranquilles;-1";
            $cotisationCVO = "0";

            if($genreKey == "EFF") {
                $cotisationDouane = "L385;Vins Mousseux;-1";
            }

            if($certificationKey != "AOC_ALSACE") {
                $cotisationCVO = "0";
            }

            $mentions = array();
            $mentions["DEFAUT"] = "";
            if($produit->hasVTSGN()) {
                //$mentions["VT"] = "VT";
                //$mentions["SGN"] = "SGN";
            }

            foreach($mentions as $mentionKey => $mentionLibelle) {
                echo "declaration;".
                     $certificationLibelle.";".$certificationKey.";". #Certification
                     ";".$genreKey.";". #Genre
                     $produit->getAppellation()->getLibelleLong().";".str_replace("appellation_", "", $produit->getAppellation()->getKey()).";". #Appellation
                     $mentionLibelle.";".$mentionKey.";". #Mention
                     $produit->getLieu()->getLibelleLong().";".str_replace("lieu", "", $produit->getLieu()->getKey()).";".
                     $produit->getCouleur()->getLibelleLong().";".strtolower(str_replace("couleur", "", $produit->getCouleur()->getKey())).";".
                     $produit->getLibelleLong().";".str_replace("cepage_", "", $produit->getKey()).";".
                     ";".$cotisationDouane.";1900-01-01;G;".$cotisationCVO.";1900-01-01;C;;;;;;;;;;;".$produit->getCodeProduit($mentionKey).";cepage;;%a% %l% %ce% %m%;certification;".
                     "\n";
            }
        }
    }
}
