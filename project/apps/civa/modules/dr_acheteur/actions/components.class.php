<?php

class dr_acheteurComponents extends sfComponents {

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspace(sfWebRequest $request) {
        $this->csv = acCouchdbManager::getClient("CSV")->retrieveByCviAndCampagne($this->etablissement->cvi);
        $this->export = acCouchdbManager::getClient()->find("EXPORT-ACHETEURS-".$this->etablissement->cvi);
    }

}
