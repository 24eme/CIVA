<?php

class ExportDRCsvTask extends sfBaseTask
{

    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('campagne', sfCommandArgument::REQUIRED, 'campagne'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'export';
        $this->name = 'dr-csv';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [maintenance:DRVT|INFO] task does things.
Call it with:

  [php symfony maintenance:DRVT|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $dr_ids = sfCouchdbManager::getClient("DR")->getAllByCampagne($arguments['campagne'], sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds();

        echo "campagne;cvi;nom;certification;genre;appellation;mention;lieu;couleur;cepage;superficie;volume;volume_revendique;usages_industriels\n";

        foreach ($dr_ids as $id) {
            if (!preg_match("/^DR-(67|68)/", $id)) {

                continue;
            }

            $dr = sfCouchdbManager::getClient("DR")->retrieveDocumentById($id, sfCouchdbClient::HYDRATE_JSON);

            if(!$dr->validee) {

                continue;
            }

            if(!isset($dr->recolte->certification->genre)) {

                continue;
            }

            foreach($dr->recolte->certification->genre as $appellation_key => $appellation) {
                if (!preg_match("/^appellation/", $appellation_key)) {

                    continue;
                }

                foreach($appellation->mention as $lieu_key => $lieu) {
                    if (!preg_match("/^lieu/", $lieu_key)) {

                        continue;
                    }

                    $total_superficie = 0;
                    $total_volume = 0;

                    foreach($lieu as $couleur_key => $couleur) {
                        if (!preg_match("/^couleur/", $couleur_key)) {

                            continue;
                        }

                        foreach($couleur as $cepage_key => $cepage) {
                            if (!preg_match("/^cepage/", $cepage_key)) {

                               continue;
                            }

                            $total_superficie += $cepage->total_superficie;
                            $total_volume += $cepage->total_volume;

                            echo sprintf("%s;%s;%s;certification;genre;%s;mention;%s;%s;%s;%01.02f;%01.02f;;\n", $dr->campagne, $dr->cvi, $dr->declarant->nom, $appellation_key, $lieu_key, $couleur_key, $cepage_key, $cepage->total_superficie, $cepage->total_volume);
                        }
                    }

                    echo sprintf("%s;%s;%s;certification;genre;%s;mention;%s;TOTAL;TOTAL;%01.02f;%01.02f;%01.02f;%01.02f\n", $dr->campagne, $dr->cvi, $dr->declarant->nom, $appellation_key, $lieu_key, $total_superficie, $total_volume, $lieu->volume_revendique, $lieu->usages_industriels_calcule);
                }
            }
        }
    }
}