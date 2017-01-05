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

        return "2016";
    }

    public function isDSDecembre() {

        return true;
    }

    public function hasCurrentFromTheFuture() {

        return false;
    }

    public function getPeriodeDS() {

        return "201612";
    }

    public function getAnneeDS($type_ds = null)
    {
        return substr($this->getPeriodeDS(), 0, 4);
    }

    public function getMonthDS($type_ds = null)
    {
        return substr($this->getPeriodeDSByType($type_ds), 4, 2);
    }

    public function getPeriodeDSByType($type_ds = null){

        return sfContext::getInstance()->getUser()->getPeriodeDS($type_ds);
    }

    /* Fin */

}
