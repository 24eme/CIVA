<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class maintenanceOuvertureFermetureDeclarationTask
 * @author mathurin
 */
class maintenanceOuvertureFermetureDeclarationTask extends sfBaseTask {

    protected function configure() {
        $this->addArguments(array(
            new sfCommandArgument('type-doc', sfCommandArgument::REQUIRED, 'Type de document'),
            new sfCommandArgument('ouverture-fermeture', sfCommandArgument::REQUIRED, 'Ouverture ou fermeture 0 | 1'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            new sfCommandOption('date', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', ''),
        ));

        $this->namespace = 'maintenance';
        $this->name = 'ouverture-fermeture-declaration';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [ouverture-fermeture-declaration|INFO] task open or close DR or DS.
Call it with:

  [php symfony OuvertureFermetureDeclaration|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        set_time_limit('240');
        ini_set('memory_limit', '512M');

        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        if (isset($arguments['type-doc']) && !empty($arguments['type-doc']) && isset($arguments['ouverture-fermeture'])) {
            $type_doc = $arguments['type-doc'];
            $ouverture_fermeture = $arguments['ouverture-fermeture'];
            if (($type_doc == "DS" || $type_doc == "DR") && ($ouverture_fermeture == 0 || $ouverture_fermeture == 1)) {                
                if ($options['date'] && $options['date'] < date('Y-m-d')) {
                    return;
                }
                $this->ouvertureFermetureDecl($type_doc, $ouverture_fermeture);
            }
        }
    }

    protected function ouvertureFermetureDecl($type_doc, $ouverture_fermeture) {
        $ouv = ($ouverture_fermeture) ? "Fermeture" : "Ouverture";
        $current = CurrentClient::getCurrent();
        if ($type_doc == "DS") {
            $current->ds_non_editable = (int) ($ouverture_fermeture+0);
            $current->save();
            echo $ouv . " des DS\n";
        }
        if ($type_doc == "DR") {
            $current->dr_non_editable = (int) ($ouverture_fermeture+0);
            $current->save();
            echo $ouv . " des DR\n";
        }
    }

}
