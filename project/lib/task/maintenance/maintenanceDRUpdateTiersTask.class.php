<?php

class maintenanceDRUpdateTiersTask extends sfBaseTask {

    protected function configure() {
        // // add your own arguments here
        $this->addArguments(array(
          new sfCommandArgument('campagne', sfCommandArgument::REQUIRED, 'Campagne'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            // add your own options here
        ));

        $this->namespace = 'maintenance';
        $this->name = 'dr-update-tiers';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [maintenance:DRVT|INFO] task does things.
Call it with:

  [php symfony maintenance:DRVT|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {

        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $dr_ids = acCouchdbManager::getClient("DR")->getAllByCampagne($arguments['campagne'], acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();

        foreach ($dr_ids as $id) {
            $dr = acCouchdbManager::getClient()->find($id);
            $old_declarant = $dr->declarant->toArray(true, false);
            $dr->storeDeclarant();
            $new_declarant = $dr->declarant->toArray(true, false);
            $diff = $this->array_diff_assoc_recursive($old_declarant, $new_declarant);


            if(count($diff )) {
              foreach($diff as $key => $value) {
                if(is_array($value)) {
                  foreach($value as $subkey => $subvalue) {
                    echo $dr->cvi.";".$key."/".$subkey.";".$old_declarant[$key][$subkey].";".$new_declarant[$key][$subkey]."\n";
                  }
                  continue;
                }
                echo $dr->cvi.";".$key.";".$old_declarant[$key].";".$new_declarant[$key]."\n";
              }
            }
        }
    }

    protected function array_diff_assoc_recursive($array1, $array2) {
    $difference=array();
    foreach($array1 as $key => $value) {
        if( is_array($value) ) {
            if(!isset($array2[$key]) || !is_array($array2[$key]) ) {
                $difference[$key] = $value;
            } else {
                $new_diff = $this->array_diff_assoc_recursive($value, $array2[$key]);
                if( !empty($new_diff) )
                    $difference[$key] = $new_diff;
            }
        } else if( !array_key_exists($key,$array2) || $array2[$key] !== $value ) {
            $difference[$key] = $value;
        }
    }
    return $difference;
}

}
