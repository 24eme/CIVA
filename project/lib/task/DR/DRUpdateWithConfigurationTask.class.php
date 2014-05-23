<?php

class DRUpdateWithConfigurationTask extends sfBaseTask
{

    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'dr';
        $this->name = 'update_with_configuration';
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

        $drs = acCouchdbManager::getClient("DR")->getAll(acCouchdbClient::HYDRATE_JSON);
        //$drs = array(acCouchdbManager::getClient("DR")->find('DR-7523700100-2011', acCouchdbClient::HYDRATE_JSON));
		
        foreach ($drs as $dr){
            $dr_clone = clone $dr;
            if (isset($dr->recolte->certification)) {

                continue;
            }

            if (count($dr->recolte) == 0) {
                
                continue;
            }

            $dr_clone->acheteurs = new stdClass();
            $dr_clone->acheteurs->certification = new stdClass();
            $dr_clone->acheteurs->certification->genre = new stdClass();
            foreach($dr->acheteurs as $acheteur_appellation_key => $acheteur_appellation) {
                $dr_clone->acheteurs->certification->genre->{$acheteur_appellation_key} = clone $acheteur_appellation;
            }

            $dr_clone->recolte = new stdClass();
            $dr_clone->recolte->certification = new stdClass();
			$dr_clone->recolte->certification->genre = new stdClass();
            
            $dr_clone->recolte->certification->total_volume = null;
            $dr_clone->recolte->certification->total_superficie = null;
            $dr_clone->recolte->certification->volume_revendique = null;
            $dr_clone->recolte->certification->dplc = null;
            $dr_clone->recolte->certification->usages_industriels_calcule = null;

            $dr_clone->recolte->certification->genre->total_volume = null;
            $dr_clone->recolte->certification->genre->total_superficie = null;
            $dr_clone->recolte->certification->genre->volume_revendique = null;
            $dr_clone->recolte->certification->genre->dplc = null;
            $dr_clone->recolte->certification->genre->usages_industriels_calcule = null;

			foreach ($dr->recolte as $key_app => $appellation){	
				if(preg_match("/^appellation_/", $key_app) && $appellation instanceof stdClass){
					$dr_clone->recolte->certification->genre->{$key_app} = clone $appellation;

                    foreach($dr_clone->recolte->certification->genre->{$key_app} as $key_lieu => $lieu) {
                        if(preg_match("/^lieu/", $key_lieu) && $lieu instanceof stdClass){
                            unset($dr_clone->recolte->certification->genre->{$key_app}->{$key_lieu});
                        }
                    }
                    
                    $dr_clone->recolte->certification->genre->{$key_app}->usages_industriels_calcule = null;

                    if(isset($appellation->dplc)) {
                        $dr_clone->recolte->certification->genre->{$key_app}->usages_industriels_calcule = $appellation->dplc;
                    }
                    $dr_clone->recolte->certification->genre->{$key_app}->mention = new stdClass();

                    $dr_clone->recolte->certification->genre->{$key_app}->mention->total_volume = null;
                    if(isset($appellation->total_volume)) {
                        $dr_clone->recolte->certification->genre->{$key_app}->mention->total_volume = $appellation->total_volume;
                    }

                    $dr_clone->recolte->certification->genre->{$key_app}->mention->total_superficie = null; 
                    if(isset($appellation->total_superficie)) {
                        $dr_clone->recolte->certification->genre->{$key_app}->mention->total_superficie = $appellation->total_superficie;
                    }
                    $dr_clone->recolte->certification->genre->{$key_app}->mention->volume_revendique = null;
                    $dr_clone->recolte->certification->genre->{$key_app}->mention->dplc = null;
                    $dr_clone->recolte->certification->genre->{$key_app}->mention->usages_industriels_calcule = null;

					foreach ($appellation as $key_lieu => $lieu){
						if(preg_match("/^lieu/", $key_lieu) && $lieu instanceof stdClass){
							$dr_clone->recolte->certification->genre->{$key_app}->mention->{$key_lieu} = clone $lieu;
                            $dr_clone->recolte->certification->genre->{$key_app}->mention->{$key_lieu}->usages_industriels_calcule = 0;
                            if (isset($lieu->dplc)) {
                                $dr_clone->recolte->certification->genre->{$key_app}->mention->{$key_lieu}->usages_industriels_calcule = $lieu->dplc;
                            }
                            $dr_clone->recolte->certification->genre->{$key_app}->mention->{$key_lieu}->usages_industriels = 0;
						}
					}		
				}	
			}
            acCouchdbManager::getClient()->storeDoc($dr_clone);
            $this->log($dr_clone->_id);
		}
	}	
}

