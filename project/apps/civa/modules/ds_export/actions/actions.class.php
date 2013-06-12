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
      set_time_limit(180);
      $this->ds = $this->getRoute()->getDS();
      $this->ds->update();
      $this->ds->storeStockage();

      $this->setLayout(false);

      $this->document = new ExportDSPdf($this->ds, array($this, 'getPartial'), $this->getRequestParameter('output', 'pdf'));
      
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

    private function ajaxPdf() {
        sfConfig::set('sf_web_debug', false);
        return $this->renderText($this->generateUrl('ds_export_pdf', $this->ds));
    }
}
