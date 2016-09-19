<?php

class exportConfigurationAttributsCsvTask extends sfBaseTask {

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
        $this->name = 'configuration-attributs-csv';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $conf = ConfigurationClient::getInstance()->find($arguments['doc_id']);
        $this->echoAttributs($conf->recolte);
    }

    protected function echoAttributs($noeud) {
        $attributs = array(
            "no_usages_industriels",
            "no_recapitulatif_couleur",
            "rendement",
            "rendement_appellation",
            "rendement_couleur",
            "rendement_mention",
            "mout",
            "auto_ds",
            "no_total_cepage",
            "detail_lieu_editable",
            "exclude_total",
            "no_vtsgn",
            "min_quantite",
            "max_quantite",
            "exclude_total",
            "superficie_optionnelle",
            "no_negociant",
            "no_cooperative",
            "no_mout",
            "no_motif_non_recolte",
            "no_dr",
            "no_ds",
        );

        foreach($attributs as $attribut) {
            $this->echoAttribut($noeud, $attribut);
        }

        if(!$noeud->getChildrenNode()) {
            return;
        }
        foreach($noeud->getChildrenNode() as $child) {
            $this->echoAttributs($child);
        }
    }

    protected function echoAttribut($noeud, $attribut) {
        if(!$noeud->exist($attribut) || is_null($noeud->_get($attribut))) {

            return;
        }

        echo HashMapper::convert($noeud->getHash()).";".$attribut.";".$noeud->_get($attribut)."\n";
    }
}
