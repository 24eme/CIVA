<?php
/**
 * Model for Current
 *
 */

class Current extends BaseCurrent {

    public function __construct() {
        parent::__construct();
        $this->set('_id', 'CURRENT');
    }

    public function getPeriode()
    {
    	return date('Y-m');
    }

    public function getConfigurationId($date) {
        foreach($this->configurations as $confDate => $confId) {
            if($date >= $confDate) {

                return $confId;
            }
        }

        throw new sfException(sprintf("Pas de configuration pour cette date %s", $date));
    }

    public function reorderConfigurations() {
        $configurations = $this->configurations->toArray(true, false);

        krsort($configurations);

        $this->remove('configurations');
        $this->add('configurations', $configurations);
    }

    public function save() {
        parent::save();
        CurrentClient::getInstance()->cacheResetConfiguration();
    }

    /* A remplacer */

    public function getCampagne() {

        return "2015";
    }

    public function isDREditable() {

        return false;
    }

    public function isDSDecembre() {

        return false;
    }

    public function getDsNonEditable() {

        return 1;
    }

    public function getDsNonOuverte() {

        return 0;
    }

    public function hasCurrentFromTheFuture() {

        return false;
    }

    public function getPeriodeDS() {

        return "201607";
    }

    public function getAnneeDS($type_ds = null)
    {
        return substr($this->getPeriodeDSByType($type_ds), 0, 4);
    }

    public function getMonthDS($type_ds = null)
    {
        return substr($this->getPeriodeDSByType($type_ds), 4, 2);
    }

    public function getPeriodeDSByType($type_ds = null){

        return $this->getPeriodeDS();
        $declarant = $this->getDeclarantDS($type_ds);
        if(CurrentClient::getCurrent()->isDSDecembre() && $declarant && $declarant->exist('ds_decembre') && $declarant->ds_decembre) {

            return CurrentClient::getCurrent()->getPeriodeDS();
        }

        if(CurrentClient::getCurrent()->isDSDecembre()) {

            return CurrentClient::getCurrent()->getAnneeDS()."07";
        }

        return CurrentClient::getCurrent()->getPeriodeDS();
    }

    /* Fin */

}
