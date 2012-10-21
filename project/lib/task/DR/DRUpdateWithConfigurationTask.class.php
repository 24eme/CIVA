<?php

class DRUpdateWithConfigurationTask extends sfBaseTask
{

    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('campagne', sfCommandArgument::REQUIRED, 'Campagne'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'dr';
        $this->name = 'update_witg_configuration';
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

		$drs[] = sfCouchdbManager::getClient("DR")->getAllByCampagne($arguments['campagne'], sfCouchdbClient::HYDRATE_JSON);

		$appellations = array();
		foreach ($drs as $dr){
			$dr = json_decode(json_encode($dr), true);
			$dr['campagne'] = "2011";
			$dr['recolte']["certification"]['genre'] = array();
			foreach ($dr['recolte']as $key_app => $appellation){	
				if(preg_match("/^appellation_/", $key_app) && is_array($appellation)){
					$dr["recolte"]["certification"]['genre'][$key_app] = $appellation;	
					$lieux=array();
					foreach ($dr["recolte"]["certification"]['genre'][$key_app] as $key_lieu => $lieu){
						if(preg_match("/^lieu/", $key_lieu) && is_array($lieu)){
							$dr["recolte"]["certification"]['genre'][$key_app]['mention'][$key_lieu] = $lieu;
								unset($dr['recolte']["certification"]['genre'][$key_app][$key_lieu]);
						}
					}		
				unset($dr['recolte'][$key_app]);
				}	
			}		
		}
		$o_dr= $this->getObjectFromArray($dr);
		$doc = sfCouchdbManager::getClient()->createDocumentFromData($o_dr);
    	$doc->save();    
	}
	
	public function getObjectFromArray($tab)
	{
		$data = new stdClass();
		if(is_array($tab) && !empty($tab))
		{
			foreach($tab as $key => $val)
			{
				if(is_array($val))
					$data->$key = $this->getObjectFromArray($val);
				else
					$data->$key = $val ;
			}
		}
		return $data ;
	}
	
}

