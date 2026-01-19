<?php

/**
 * printable actions.
 *
 * @package    civa
 * @subpackage printable
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */

class dr_exportActions extends _DRActions {

    public function executeXml(sfWebRequest $request) {
        $this->secureDR(DRSecurity::CONSULTATION);
        $dr = $this->getRoute()->getDR();
        $tiers = $this->getRoute()->getEtablissement();

        try {
            if (!$dr->updated)
                throw new Exception();
        }catch(Exception $e) {
            $dr->update();
            $dr->save();
        }

        $xml = new ExportDRXml($dr, array($this, 'getPartial'), $request->getParameter('destinataire', ExportDRXml::DEST_DOUANE));
        $this->response->setContentType('text/xml');
        return $this->renderText($xml->getContent());
    }

    public function executeJson(sfWebRequest $request)
    {
        $dr = $this->getRoute()->getDR();

        if ($dr->isValideeCiva() === false || $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN) === false) {
            return $this->redirect('dr_visualisation', $dr);
        }

        $class = "Export".$dr->getType()."Json";
        $json = [$class::ROOT_NODE => []];

        $export = new $class($dr);
        $export->build();
        $json[$class::ROOT_NODE][] = json_decode($export->export());

        if(sfConfig::get('sf_debug')) {
            $this->getResponse()->setHttpHeader('Content-Type', 'application/json');
        } else {
            $export->addHeaders($this->getResponse());
        }
        return $this->renderText(json_encode($json, JSON_PRETTY_PRINT).PHP_EOL);
    }

    private function ajaxPdf($from_csv = false) {
        sfConfig::set('sf_web_debug', false);
        return $this->renderText($this->generateUrl('dr_pdf', array('identifiant' => $this->etablissement->identifiant, 'annee' => $this->annee, 'from_csv' => $from_csv)));
    }
    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executePdf(sfWebRequest $request) {

        set_time_limit(180);
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->annee = $request->getParameter('annee');

        if ($request->getParameter("from_csv", null)) {
            $import_from = array();
            $this->dr = acCouchdbManager::getClient('DR')->createFromCSVRecoltant($this->annee, $this->etablissement, $import_from, $this->getUser()->isSimpleOperateur());
        }

        if(!$this->dr) {
            $this->dr = DRClient::getInstance()->find('DR-'.$this->etablissement->identifiant.'-'.$this->annee);
        }

        if(!$this->dr) {

            return $this->forward404();
        }

        if(!DRSecurity::getInstance($this->dr)->isAuthorized(DRSecurity::CONSULTATION)) {

            return $this->forwardSecure();
        }

        $this->setLayout(false);

        try {
            if (!$this->dr->updated)
                throw new Exception();
        }catch(Exception $e) {
            $this->dr->update();
            $this->dr->cleanNoeuds();
            $this->dr->save();
        }

        if(!$this->dr->isValideeCiva()) {
            $this->dr->cleanNoeuds();
            $this->dr->storeDeclarant();
        }

        $this->forward404Unless($this->dr);

        $this->document = @(new ExportDRPdf($this->dr, array($this, 'getPartial'), $this->getRequestParameter('output', 'pdf')));

        if($request->getParameter('force') || $request->getParameter("from_csv")) {
            $this->document->removeCache();
        }
        $this->document->generatePDF();

        if ($request->getParameter('ajax')) {
            return $this->ajaxPdf($request->getParameter("from_csv", null));
        }

        $this->document->addHeaders($this->getResponse());

        return $this->renderText($this->document->output());
    }


    public function executeComptesCSV(sfWebRequest $request) {

        $this->setResponseCsv('comptes.csv');
        return $this->renderText(file_get_contents(sfConfig::get('sf_data_dir').'/export/comptes/comptes.csv'));
    }

    public function executeCsv(sfWebRequest $request) {
        $this->secureDR(DRSecurity::CONSULTATION);
        $dr = $this->getRoute()->getDr();
        $csvContruct = new ExportDRCsv($dr->campagne, $dr->cvi);
        $csvContruct->export();

         return $this->renderText($csvContruct->output());
    }

    public function executeCsvTiersDREncours(sfWebRequest $request) {
        set_time_limit('240');
        ini_set('memory_limit', '512M');
        $recoltants = acCouchdbManager::getClient("Recoltant")->getAll(acCouchdbClient::HYDRATE_JSON);
        $values = array();
        $values[] = array("cvi", "nom", "commune de déclaration", "téléphone", "e-mail", "étape");
        foreach ($recoltants as $item) {
            if ($item->cvi != "7523700100") {
                $dr = acCouchdbManager::getClient("DR")->retrieveByCampagneAndCvi($item->cvi, $this->getUser()->getCampagne(), acCouchdbClient::HYDRATE_JSON);
                if ($dr && (!isset($dr->validee) || !$dr->validee)) {
                    if($dr->type == "LS") {

                        continue;
                    }
                    $compte = acCouchdbManager::getClient()->find($item->compte[0], acCouchdbClient::HYDRATE_JSON);
                    $ligne = array();
                    $ligne[] = $item->cvi;
                    $ligne[] = $item->nom;
                    $ligne[] = $item->declaration_commune;
                    $ligne[] = $item->telephone;
                    $ligne[] = $compte->email;
                    $ligne[] = isset($dr->etape) ? $dr->etape : null;
                    $values[] = $ligne;
                }
            }
        }

        $this->setResponseCsv('declaration_recolte_en_cours.csv');
        return $this->renderText(Tools::getCsvFromArray($values));
    }

    public function executeDrAcheteurCsv(sfWebRequest $request) {

        throw new sfException("Export interdit");

        ini_set('memory_limit', '128M');
        set_time_limit(180);
        $filename = $this->getUser()->getCampagne().'_DR_ACHETEUR_'.$this->getUser()->getTiers('Acheteur')->cvi;

        $export = new ExportDRAcheteurCsv($this->getUser()->getCampagne(), $this->getUser()->getTiers('Acheteur')->cvi);
        $existing_file = sfConfig::get('sf_data_dir').'/export/dr-acheteur/csv/'.$this->getUser()->getCampagne().'/'.$filename.'_'.$export->getMd5().'.csv';

        if (!$request->hasParameter('force') && file_exists($existing_file)) {
            $content = file_get_contents($existing_file);
        } else {
            $export->export();
            $content = $export->output();
            file_put_contents($existing_file, $content);
        }

        $this->setResponseCsv($filename.'.csv');
        return $this->renderText($content);
    }

    public function executeDrValideeCsv(sfWebRequest $request) {

        throw new sfException("Export interdit");

        ini_set('memory_limit', '128M');
        set_time_limit(180);

        $filename = $this->getUser()->getCampagne().'_UTILISATEURS_DR_VALIDEE_'.$this->getUser()->getTiers('Acheteur')->cvi;

        $export = new ExportUserDRValideeCsv($this->getUser()->getCampagne(), $this->getUser()->getTiers('Acheteur')->cvi);
        $export->export();
        $content = $export->output();

        $this->setResponseCsv($filename.'.csv');
        return $this->renderText($content);
    }

    protected function setResponseCsv($filename) {
        $this->response->setContentType('application/csv');
        $this->response->setHttpHeader('Content-disposition', 'filename='.$filename, true);
        $this->response->setHttpHeader('Pragma', 'o-cache', true);
        $this->response->setHttpHeader('Expires', '0', true);
    }

    protected function formatModifiedValue($keys, $values, $values_changed, $indicator = '*') {
        foreach($keys as $key => $value_key) {
            if (array_key_exists($key, $values_changed)) {
                if (is_array($value_key)) {
                    return $this->formatModifiedValue($value_key, $values[$key], $values_changed[$key]);
                } else {
                    return $indicator.$values[$key];
                }
            } elseif (is_array($value_key)) {
                return $this->formatModifiedValue($value_key, $values[$key], array());
            } else {
                return $values[$key];
            }
        }
        return '';
    }
}
