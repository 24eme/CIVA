<?php

/**
 * vrac_export actions.
 * Mathurin Petit
 * 2013-09-23
 */
class vrac_exportActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
    public function executePDF(sfWebRequest $request)
    {
      set_time_limit(180);
      $this->vrac = $this->getRoute()->getVrac();
      $this->secureVrac(VracSecurity::CONSULTATION, $this->vrac);

      if ($request->isXmlHttpRequest()) {

          return $this->ajaxPdf();
      }

      $this->setLayout(false);

      if (($pdfHistorise = $this->vrac->getPDFhistoriseContent()) && !$request->getParameter('force')) {
          $pdf = new PageablePDF('', '');
          $pdf->addHeaders($this->getResponse());
          return $this->renderText($pdfHistorise);
      }

      $odg = $request->getParameter('odg');

      $this->document = @new ExportVracPdf($this->vrac, $odg, array($this, 'getPartial'), $this->getRequestParameter('output', 'pdf'));

      if($request->getParameter('force')) {
        $this->document->removeCache();
      }
      $this->document->generatePDF();

      $this->document->addHeaders($this->getResponse());

      return $this->renderText($this->document->output());
    }

    public function executeAnnexe(sfWebRequest $request)
    {
        $type_contrat = $request->getParameter('type_contrat', VracClient::TYPE_VRAC);
        @$document = new PageablePDF("VERSO DU CONTRAT DE VENTE EN VRAC", "DE VINS AOC PRODUITS EN ALSACE", "verso_contrat_".$type_contrat.".pdf", null, ' de ', 'P', ExportVracPdf::getConfig());
        $document->addPage($this->getPartial('vrac_export/annexe', array('type_contrat' => $type_contrat, 'clause_reserve_propriete' => $request->getParameter('clause_reserve_propriete', false))));
        $document->generatePDF();
        $document->addHeaders($this->getResponse());

        return $this->renderText(@$document->output());
    }

    public function executeMain()
	{
	}

    protected function setResponseCsv($filename) {
        $this->response->setContentType('application/csv');
        $this->response->setHttpHeader('Content-disposition', 'filename='.$filename, true);
        $this->response->setHttpHeader('Pragma', 'o-cache', true);
        $this->response->setHttpHeader('Expires', '0', true);
    }

    private function ajaxPdf() {
        sfConfig::set('sf_web_debug', false);
        return $this->renderText($this->generateUrl('vrac_export_pdf', $this->vrac));
    }

    protected function secureVrac($droits, $vrac) {
        if(!isset($this->compte)) {
            $this->compte = $this->getUser()->getCompte();
        }
        if(!VracSecurity::getInstance($this->compte, $vrac)->isAuthorized($droits)) {

            return $this->forwardSecure();
        }
    }

    protected function forwardSecure()
    {
        $this->context->getController()->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));

        throw new sfStopException();
    }
}
