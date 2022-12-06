<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class updateDateMercurialeVracTask
 */
class updateDateMercurialeVracTask extends sfBaseTask
{

    protected function configure()
    {
        $this->addArguments(array(
            new sfCommandArgument('vracid', sfCommandArgument::REQUIRED, 'vrac id'),
            new sfCommandArgument('date', sfCommandArgument::REQUIRED, 'date validation'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'mercuriale';
        $this->name = 'update-date';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [mercurialeVrac|INFO] task does things.
Call it with:

  [php symfony mercuriale:update-date|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        if ($vrac = VracClient::getInstance()->find($arguments['vracid'])) {
            if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $arguments['date'])) {
                $vrac->campagne = VracClient::getInstance()->buildCampagneVrac($arguments['date']);
                $vrac->date_modification = $arguments['date'];
                $vrac->valide->date_validation = $arguments['date'];
                $vrac->forceSave();
                echo sprintf("%s updated\n", $arguments['vracid']);
            } else {
                echo sprintf("Erreur : date format invalide %s\n", $arguments['date']);
            }
        } else {
            echo sprintf("Erreur : vracid inexistant %s\n", $arguments['vracid']);
        }
    }

}
