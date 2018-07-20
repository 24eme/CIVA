<?php

/**
 * ds_export actions.
 *
 * @package    civa
 * @subpackage ds_export
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ds_exportActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
    public function executePDF(sfWebRequest $request)
    {
          $this->ds = $this->getRoute()->getDS();
      if(!DSSecurity::getInstance($this->ds->getEtablissement(), $this->getRoute()->getDS())->isAuthorized(array(DSSecurity::CONSULTATION))) {

         throw new sfSecurityException("Vous n'avez pas accès à cette DS");
      }

      set_time_limit(180);
      $this->ds = $this->getRoute()->getDS();
      $this->ds->update();
      $this->ds->storeStockage();

      $this->setLayout(false);

      $this->document = @(new ExportDSPdf($this->ds, array($this, 'getPartial'), true, $this->getRequestParameter('output', 'pdf')));

      if($request->getParameter('force')) {
        $this->document->removeCache();
      }
      $this->document->generatePDF();

      if ($request->isXmlHttpRequest()) {

          return $this->ajaxPdf();
      }

      $this->document->addHeaders($this->getResponse());

      return $this->renderText($this->document->output());
    }


    public function executePDFEmpty(sfWebRequest $request)
    {
      $type_ds = $request->getParameter('type');

      $this->tiers = $this->getUser()->getDeclarantDS($type_ds);

      set_time_limit(180);

      $this->setLayout(false);

      $this->document = new ExportDSPdfEmpty($this->tiers, $type_ds, array($this, 'getPartial'), true, $this->getRequestParameter('output', 'pdf'), null, $this->getRequestParameter('force', 0));

      if($request->getParameter('force')) {
        $this->document->removeCache();
      }
      $this->document->generatePDF();

      if ($request->isXmlHttpRequest()) {

          return $this->ajaxPdf();
      }
      $this->document->addHeaders($this->getResponse());

      return $this->renderText($this->document->output());
    }

    public function executeCsvEnCours(sfWebRequest $request) {
      ini_set('memory_limit', '128M');
      set_time_limit(180);

      $ds_non_validees = acCouchdbManager::getClient()->reduce(false)
                                                      ->startkey(array("2012-2013", false))
                                                      ->endkey(array("2012-2013", false, array()))
                                                      ->getView("STATS", "DS");
      $values = array();
      $values[] = array("cvi", "nom", "commune de déclaration", "téléphone", "e-mail", "étape");
      foreach ($ds_non_validees->rows as $row) {
          $ds = acCouchdbManager::getClient()->find($row->id, acCouchdbClient::HYDRATE_JSON);
          $ligne = array();
          $ligne[] = $ds->declarant->cvi;
          $ligne[] = $ds->declarant->nom;
          $ligne[] = $ds->declaration_commune;
          $ligne[] = $ds->declarant->telephone;
          $ligne[] = $ds->declarant->email;
          $ligne[] = $ds->num_etape ? DSCivaClient::$etapes[$ds->num_etape] : null;
          $values[] = $ligne;
      }

      $this->setResponseCsv('declaration_stocks_en_cours.csv');

      return $this->renderText(Tools::getCsvFromArray($values));
    }

    protected function setResponseCsv($filename) {
        $this->response->setContentType('application/csv');
        $this->response->setHttpHeader('Content-disposition', 'filename='.$filename, true);
        $this->response->setHttpHeader('Pragma', 'o-cache', true);
        $this->response->setHttpHeader('Expires', '0', true);
    }

    private function ajaxPdf() {
        sfConfig::set('sf_web_debug', false);
        return $this->renderText($this->generateUrl('ds_export_pdf', $this->ds));
    }
}
