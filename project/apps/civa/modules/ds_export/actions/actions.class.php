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
    $ds = $this->getRoute()->getDS();
    $ds->update();

    $this->setLayout(false);

    $this->document = new ExportDSPdf($ds, array($this, 'getPartial'), $this->getRequestParameter('output', 'pdf'));

    if($request->getParameter('force')) {
        $this->document->removeCache();
    }

    $this->document->generatePDF();

    /*if ($request->getParameter('ajax')) {
        return $this->ajaxPdf($request->getParameter("from_csv", null));
    }*/

    $this->document->addHeaders($this->getResponse());

    return $this->renderText($this->document->output());
  }
}
