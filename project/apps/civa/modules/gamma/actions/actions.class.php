<?php

/**
 * gamma actions.
 *
 * @package    civa
 * @subpackage gamma
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class gammaActions extends sfActions
{
    /**
     *
     * @param sfWebRequest $request
     */
    public function executeProcess(sfWebRequest $request) {
        $inscription = $request->getParameter('gamma_inscription');
        $this->tiers = $this->getUser()->getTiers('MetteurEnMarche');
	$type = $request->getParameter('gamma') ;
        if (isset($inscription) && $inscription['choix']) {
		$this->tiers->add('gamma');
                $this->tiers->gamma->statut = "INSCRIT";
                $this->tiers->gamma->num_cotisant = $this->getUser()->getCompte()->login;
		$this->tiers->save();
		return $this->redirect(sfConfig::get('app_gamma_url_prod'));
	}
	if (isset($inscription) || !isset($type)) {
		return $this->redirect('@mon_espace_civa_gamma');
	}
        if ($type['type_acces'] == 'plateforme') {
            return $this->redirect(sfConfig::get('app_gamma_url_prod'));
        }
	return $this->redirect(sfConfig::get('app_gamma_url_qualif'));
    }
   
    /**
     *
     * @param sfWebRequest $request
     */
    public function executeDownloadNotice(sfWebRequest $request) {
        return $this->renderPdf(sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."images/aide_gamma.pdf", "aide_gamma.pdf");
    }
    
    /**
     *
     * @param sfWebRequest $request
     */
    public function executeDownloadNomenclatures(sfWebRequest $request) {
        return $this->renderPdf(sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."images/liste_nomenclatures_douanieres.pdf", "liste_nomenclatures_douanieres.pdf");
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeDownloadAdhesion(sfWebRequest $request) {
        return $this->renderPdf(sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."images/AdhesionGamma_EDI_CIVA.pdf", "AdhesionGamma_EDI_CIVA.pdf");
    }
    
    protected function renderPdf($path, $filename) {
        $this->getResponse()->setHttpHeader('Content-Type', 'application/pdf');
        $this->getResponse()->setHttpHeader('Content-disposition', 'attachment; filename="'.$filename.'"');
        $this->getResponse()->setHttpHeader('Content-Transfer-Encoding', 'binary');
        $this->getResponse()->setHttpHeader('Content-Length', filesize($path));
        $this->getResponse()->setHttpHeader('Pragma', '');
        $this->getResponse()->setHttpHeader('Cache-Control', 'public');
        $this->getResponse()->setHttpHeader('Expires', '0');
        return $this->renderText(file_get_contents($path));
    }
}
