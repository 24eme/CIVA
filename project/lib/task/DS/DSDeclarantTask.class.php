<?php

class DSDeclarantTask extends sfBaseTask
{

    protected $debug = false;
    protected $periode = null;
    
    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('periode', sfCommandArgument::REQUIRED, 'periode'),
            new sfCommandArgument('id_compte', sfCommandArgument::REQUIRED, 'id_compte'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'ds';
        $this->name = 'declarant';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
Permet d'exporter les infos d"un declarant de la DS
Call it with:
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        sfContext::createInstance($this->configuration);
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        if(!array_key_exists('periode',$arguments) || !preg_match('/^([0-9]{6})$/', $arguments['periode'])){
            throw new sfException("La periode doit être passé en argument et doit être de la forme AAAAMM");
        }
        $this->periode = $arguments['periode'];

        $compte = _CompteClient::getInstance()->find($arguments["id_compte"]);

        if(!$compte){
            
            return;
        }
        if($compte->type != "CompteTiers") {
            
            return;
        }

        if(!$compte->isActif()){
            
            return;
        }

        if(!$compte->hasDroit(_CompteClient::DROIT_DS_DECLARANT)) {
            return;
        }

        $this->executeDeclarant($compte, DSCivaClient::TYPE_DS_PROPRIETE);
        $this->executeDeclarant($compte, DSCivaClient::TYPE_DS_NEGOCE);
    }
    
    public function executeDeclarant($compte, $type_ds)
    {   
        $tiers = $compte->getDeclarantDS($type_ds);

        if(!$tiers) {

            return;
        }

        if($tiers->type == "MetteurEnMarche" && $tiers->cvi && $tiers->cvi == $compte->login) {
            $tiersAchat = AcheteurClient::getInstance()->findByCvi($tiers->cvi);
            if($tiersAchat) {
                $compteAchat = $tiersAchat->getCompteObject();
                $compteMetteurEnMarche = $tiers->getCompteObject();

                if($compte->_id == $compteAchat->_id && $compteMetteurEnMarche->isInscrit() && $compteMetteurEnMarche->email && $compteMetteurEnMarche->isActif() && $compteMetteurEnMarche->hasDroit(_CompteClient::DROIT_DS_DECLARANT)) {

                    return;
                }
            }
        }

        if(!$tiers->hasLieuxStockage() && !$tiers->isAjoutLieuxDeStockage()) {

            echo $type_ds.";ERROR;PAS DE LIEU DE STOCKAGE;".$compte->_id."\n";
            return;
        }

        $dsCurrent = $tiers->getDs($this->periode);

        $hasDS = ($dsCurrent) ? "1" : "0";

        echo sprintf("%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s\n", $type_ds, $hasDS, $compte->login, $compte->email, $tiers->cvi, $tiers->civaba, $tiers->categorie, $tiers->qualite_categorie, $tiers->nom, $tiers->siege->commune, $compte->_id, $tiers->_id);
    }
}
