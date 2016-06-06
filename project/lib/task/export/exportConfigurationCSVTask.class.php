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
            $genreKey = "TRANQ";

            if($produit->getAppellation()->getKey() == "appellation_VINTABLE") {
                $certificationKey = "VINSSIG";
                $certificationLibelle = "";
            }

            if($produit->getAppellation()->getKey() == "appellation_CREMANT") {
                $genreKey = "EFF";
            }


            echo "declaration;".
                 ";".$certificationKey.";". #Certification
                 ";".$genreKey.";". #Genre
                 $produit->getAppellation()->getLibelleLong().";".str_replace("appellation_", "", $produit->getAppellation()->getKey()).";". #Appellation
                 ";;". #Mention
                 $produit->getLieu()->getLibelleLong().";".str_replace("lieu", "", $produit->getLieu()->getKey()).";".
                 $produit->getCouleur()->getLibelleLong().";".strtolower(str_replace("couleur", "", $produit->getCouleur()->getKey())).";".
                 $produit->getLibelleLong().";".str_replace("cepage_", "", $produit->getKey()).";".
                 ";;L387;Vins Tranquilles;3,75;2015-08-01;G;5;2015-08-01;G;;;;;;;;;;;;;;%c% %a% %l% %ce;certification;".
                 "\n";
        }
    }
}
