<?php

class maintenanceMutualisationCompteRemplacementDocTask extends sfBaseTask
{
    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
           new sfCommandArgument('doc_id', sfCommandArgument::REQUIRED, 'My argument'),
        ));

        $this->addOptions(array(
          new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
          new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
          new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
          // add your own options here
        ));

        $this->namespace        = 'maintenance';
        $this->name             = 'mutualisation-compte-remplacement-doc';
        $this->briefDescription = '';
        $this->detailedDescription = '';
    }

    protected function execute($arguments = array(), $options = array())
    {
        //initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $vrac = VracClient::getInstance()->find($arguments['doc_id'], acCouchdbClient::HYDRATE_JSON);

        if($this->replace($vrac, "")) {
            VracClient::getInstance()->storeDoc($vrac);
            echo "Enregistrement du doc ".$vrac->_id."\n";
        }
    }

    public function replace(&$iterable, $parentKey) {
        $modifie = false;
        $keys2Replace = array();
        foreach($iterable as $key => $value) {
            if($this->isTiers($key)) {
                $keys2Replace[$key] = $this->tiersId2EtablissementId($key);
            }

            if(is_array($value) || is_object($value)) {

                $modifie = ($this->replace($value, $parentKey."/".$key)) ? true : $modifie;
                continue;
            }

            if(!is_string($value)) {

                continue;
            }

            if(!$this->isTiers($value)) {
                continue;
            }

            $newValue = $this->tiersId2EtablissementId($value);
            if($this->replaceValue($iterable, $key, $newValue)) {
                echo $parentKey."/".$key.":".$value." => ".$newValue ."\n";
            }
            $modifie = true;
        }

        foreach($keys2Replace as $key => $newKey) {
            if($this->replaceKey($iterable, $key, $newKey)) {
                echo $parentKey."/".$key." => ".$parentKey."/".$newKey." \n";
            }
            $modifie = true;
        }

        return $modifie;
    }

    public function replaceKey($iterable, $key, $newKey) {
        if(!$newKey) {
            return false;
        }

        if(is_array($iterable)) {
            $value = $iterable[$key];
            $iterable[$newKey] = $value;
            unset($iterable[$key]);
        }

        if(is_object($iterable)) {
            $value = $iterable->{$key};
            $iterable->{$newKey} = $value;
            unset($iterable->{$key});
        }

        return true;
    }

    public function replaceValue($iterable, $key, $newValue) {
        if(!$newValue) {
            return false;
        }

        if(is_array($iterable)) {
            $iterable[$key] = $newValue;
        }

        if(is_object($iterable)) {
            $iterable->{$key} = $newValue;
        }

        return false;
    }

    public function isTiers($value) {

        return preg_match("/^(REC|MET|ACHAT)-[0-9]+$/", $value);
    }

    public function tiersId2EtablissementId($value) {
        $tiers = _TiersClient::getInstance()->find($value, acCouchdbClient::HYDRATE_JSON);

        if(!$tiers) {
            return;
        }

        $etablissement = EtablissementClient::getInstance()->find("ETABLISSEMENT-".$tiers->cvi, acCouchdbClient::HYDRATE_JSON);

        if($etablissement)  {

            return $etablissement->_id;
        }

        $etablissement = EtablissementClient::getInstance()->find("ETABLISSEMENT-C".$tiers->civaba, acCouchdbClient::HYDRATE_JSON);

        if($etablissement)  {

            return $etablissement->_id;
        }

        if($etablissement = EtablissementClient::getInstance()->find("ETABLISSEMENT-".str_replace("COMPTE-", "", $tiers->compte[0]), acCouchdbClient::HYDRATE_JSON))  {

            return $etablissement->_id;
        }

        return null;
    }

}
