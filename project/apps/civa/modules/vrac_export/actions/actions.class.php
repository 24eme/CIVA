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
        $temporalite = $request->getParameter('temporalite', VracClient::TEMPORALITE_ANNUEL);
        $temporalite = strtolower($temporalite);
        if (in_array($temporalite, ['annuel', 'pluriannuel']) === false) {
            $temporalite = 'annuel';
        }
        $filename = sprintf('contrat_de_vente_%s_%s_verso.pdf', $temporalite, strtolower($type_contrat));
        $path_verso = Document::getLastByFilename(sfConfig::get('sf_web_dir').'/helpPdf/', $filename);
        $this->getResponse()->setHttpHeader('Content-Type', 'application/pdf');
        $this->getResponse()->setHttpHeader('Content-disposition', 'attachment; filename="' . basename($filename) . '"');
        $this->getResponse()->setHttpHeader('Content-Transfer-Encoding', 'binary');
        $this->getResponse()->setHttpHeader('Pragma', '');
        $this->getResponse()->setHttpHeader('Cache-Control', 'public');
        $this->getResponse()->setHttpHeader('Expires', '0');
        return $this->renderText(file_get_contents($path_verso));
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
