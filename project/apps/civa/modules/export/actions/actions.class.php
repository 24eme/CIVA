<?php

/**
 * printable actions.
 *
 * @package    civa
 * @subpackage printable
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */

class exportActions extends sfActions {
    
    public function executeXml(sfWebRequest $request) {
        $tiers = $this->getUser()->getTiers();
        $this->annee = $this->getRequestParameter('annee', $this->getUser()->getCampagne());
        $key = 'DR-'.$tiers->cvi.'-'.$this->annee;
        $dr = sfCouchdbManager::getClient()->retrieveDocumentById($key);

        try {
            if (!$dr->updated)
                throw new Exception();
        }catch(Exception $e) {
            $dr->update();
            $dr->save();
        }

        $xml = new ExportDRXml($dr, array($this, 'getPartial'));
        $this->response->setContentType('text/xml');
        return $this->renderText($xml->getContent());
    }

    private function ajaxPdf($from_csv = false) {
        sfConfig::set('sf_web_debug', false);
        return $this->renderText($this->generateUrl('print', array('annee'=>$this->annee, 'from_csv' => $from_csv)));
    }
    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executePdf(sfWebRequest $request) {
        set_time_limit(180);
        $tiers = $this->getUser()->getTiers();
        $this->annee = $this->getRequestParameter('annee', $this->getUser()->getCampagne());

        $key = 'DR-'.$tiers->cvi.'-'.$this->annee;
        if ($request->getParameter("from_csv", null)) {
            $import_from = array();
            $dr = sfCouchdbManager::getClient('DR')->createFromCSVRecoltant($this->annee, $tiers, $import_from);
        } else {
            $dr = sfCouchdbManager::getClient()->retrieveDocumentById($key);
        }
       
        $this->setLayout(false);

        try {
            if (!$dr->updated)
                throw new Exception();
        }catch(Exception $e) {
            $dr->update();
            $dr->save();
        }
        $this->forward404Unless($dr);

        $this->document = new ExportDRPdf($dr, $tiers, array($this, 'getPartial'), $this->getRequestParameter('output', 'pdf'));

        if($request->getParameter('force')) {
            $this->document->removeCache();
        }
        $this->document->generatePDF();

        if ($request->getParameter('ajax')) {
            return $this->ajaxPdf($request->getParameter("from_csv", null));
        }

        $this->document->addHeaders($this->getResponse());

        return $this->renderText($this->document->output());
    }

    public function executeCsvTiers(sfWebRequest $request) {
        set_time_limit('240');
        ini_set('memory_limit', '512M');
        $tiers = sfCouchdbManager::getClient("Tiers")->getAll(sfCouchdbClient::HYDRATE_JSON);
        $values = array();
        foreach ($tiers as $item) {
            if ($item->recoltant == 1 && $item->cvi != "7523700100") {
                $ligne = array();
                $ligne[] = $item->cvi;
                if (strpos('{TEXT}', $item->mot_de_passe) === false) {
                    $ligne[] = str_replace('{TEXT}', '', $item->mot_de_passe);
                } else {
                    $ligne[] = "code activé";
                }
                $ligne[] = $item->nom;
                $ligne[] = $item->siege->adresse;
                $ligne[] = $item->siege->code_postal;
                $ligne[] = $item->siege->commune;
                $ligne[] = $item->no_accises;

                $values[] = $ligne;
            }
        }

        $this->setResponseCsv('tiers.csv');
        return $this->renderText(Tools::getCsvFromArray($values));
    }

    public function executeCsvTiersDREncours(sfWebRequest $request) {
        set_time_limit('240');
        ini_set('memory_limit', '512M');
        $recoltants = sfCouchdbManager::getClient("Recoltant")->getAll(sfCouchdbClient::HYDRATE_JSON);
        $values = array();
        foreach ($recoltants as $item) {
            if ($item->cvi != "7523700100") {
                $dr = sfCouchdbManager::getClient("DR")->retrieveByCampagneAndCvi($item->cvi, $this->getUser()->getCampagne(), sfCouchdbClient::HYDRATE_JSON);
                if ($dr && (!isset($dr->validee) || !$dr->validee)) {
                    $compte = sfCouchdbManager::getClient()->retrieveDocumentById($item->compte[0], sfCouchdbClient::HYDRATE_JSON);
                    $ligne = array();
                    $ligne[] = $item->cvi;
                    $ligne[] = $item->nom;
                    $ligne[] = $item->declaration_commune;
                    $ligne[] = $item->telephone;
                    $ligne[] = $compte->email;
                    $ligne[] = $compte->statut;
                    $ligne[] = $dr->etape;
                    $values[] = $ligne;
                }
            }
        }

        $this->setResponseCsv('recoltant_declaration_en_cours.csv');
        return $this->renderText(Tools::getCsvFromArray($values));
    }

    public function executeCsvTiersNonValideeCiva(sfWebRequest $request) {
        set_time_limit('240');
        ini_set('memory_limit', '512M');
        $tiers = sfCouchdbManager::getClient("Tiers")->getAllCvi(sfCouchdbClient::HYDRATE_JSON);
        $values = array();
        foreach ($tiers as $item) {
            if ($item->cvi != "7523700100") {
                $dr = sfCouchdbManager::getClient("DR")->retrieveByCampagneAndCvi($item->cvi, $this->getUser()->getCampagne(), sfCouchdbClient::HYDRATE_JSON);
                if ($dr && isset($dr->validee) && $dr->validee && (!isset($dr->modifiee) || !$dr->modifiee)) {
                    $ligne = array();
                    $ligne[] = $item->cvi;
                    $ligne[] = $item->nom;
                    $ligne[] = $item->declaration_commune;
                    $ligne[] = $item->telephone;
                    $ligne[] = $item->email;
                    $inscrit = 'non_inscrit';
                    if (substr($item->mot_de_passe, 0, 6) !== "{TEXT}") {
                        $inscrit = 'inscrit';
                    }
                    $ligne[] = $inscrit;
                    $ligne[] = $dr->etape;
                    $values[] = $ligne;
                }
            }
        }
        $this->setResponseCsv('tiers.csv');
        return $this->renderText(Tools::getCsvFromArray($values));
    }
    
    public function executeDrAcheteurCsv(sfWebRequest $request) {
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
