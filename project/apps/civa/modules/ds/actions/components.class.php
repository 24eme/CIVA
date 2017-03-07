<?php

class dsComponents extends sfComponents {



    /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspace(sfWebRequest $request) {
        $this->periode = CurrentClient::getCurrent()->getPeriodeDSByType($this->type_ds);
        $this->ds = DSCivaClient::getInstance()->findPrincipaleByEtablissementAndPeriode($this->type_ds, $this->etablissement, $this->periode);
        $this->ds_editable = DSCivaClient::getInstance()->isTeledeclarationOuverte();
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspaceEnCours(sfWebRequest $request) {
        $this->periode = CurrentClient::getCurrent()->getPeriodeDSByType($this->type_ds);
        $this->ds = DSCivaClient::getInstance()->findPrincipaleByEtablissementAndPeriode($this->type_ds, $this->etablissement, $this->periode);
    }

        /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspaceValidee(sfWebRequest $request) {
        $this->periode = CurrentClient::getCurrent()->getPeriodeDSByType($this->type_ds);
        $this->ds = DSCivaClient::getInstance()->findPrincipaleByEtablissementAndPeriode($this->type_ds, $this->etablissement, $this->periode);
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspaceColonne(sfWebRequest $request) {
        $this->periode = CurrentClient::getCurrent()->getPeriodeDSByType($this->type_ds);
        $this->dsByperiodes = DSCivaClient::getInstance()->findDsPrincipalesByPeriodeAndEtablissement($this->type_ds, $this->etablissement, $this->periode - 1);

        krsort($this->dsByperiodes);
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspaceNoLieuxStockage(sfWebRequest $request) {
    }


}
