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
        $compte = $this->getUser()->getCompte();
        $etablissement = $this->getRoute()->getEtablissement();
        $isInscrit = GammaClient::getInstance()->findByEtablissement($etablissement);

        if (!$isInscrit) {
		    $gamma = GammaClient::getInstance()->createOrFind($etablissement);
            GammaClient::getInstance()->storeDoc($gamma);
        }

        if(!$compte->exist('gecos') || GammaClient::getInstance()->getGecos($compte, $etablissement) != $compte->gecos) {
            $compte->add('gecos', GammaClient::getInstance()->getGecos($compte, $etablissement));
            $compte->save();
        }

        return $this->redirect(sfConfig::get('app_gamma_url_prod'));
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
    public function executeDownloadEnlevementPropriete(sfWebRequest $request) {
        return $this->renderPdf(sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."images/procedure_enlevement_propriete.pdf", "procedure_enlevement_propriete.pdf");
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
