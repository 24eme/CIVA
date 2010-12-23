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
        $this->annee = $this->getRequestParameter('annee', null);
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

    private function ajaxPdf() {
        sfConfig::set('sf_web_debug', false);
        return $this->renderText($this->generateUrl('print', array('annee'=>$this->annee)));
    }
    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executePdf(sfWebRequest $request) {
        $tiers = $this->getUser()->getTiers();
        $this->annee = $this->getRequestParameter('annee', null);

        $key = 'DR-'.$tiers->cvi.'-'.$this->annee;
        $dr = sfCouchdbManager::getClient()->retrieveDocumentById($key);
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
            return $this->ajaxPdf();
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
        $tiers = sfCouchdbManager::getClient("Tiers")->getAll(sfCouchdbClient::HYDRATE_JSON);
        $values = array();
        foreach ($tiers as $item) {
            if ($item->cvi != "7523700100") {
                $dr = sfCouchdbManager::getClient("DR")->retrieveByCampagneAndCvi($item->cvi, $this->getUser()->getCampagne(), sfCouchdbClient::HYDRATE_JSON);
                if ($dr && (!isset($dr->validee) || !$dr->validee)) {
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

    public function executeCsvTiersNonValideeCiva(sfWebRequest $request) {
        set_time_limit('240');
        ini_set('memory_limit', '512M');
        $tiers = sfCouchdbManager::getClient("Tiers")->getAll(sfCouchdbClient::HYDRATE_JSON);
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

    public function executeCsvTiersModifications(sfWebRequest $request) {
        set_time_limit(0);
        $tiers_ids = sfCouchdbManager::getClient("Tiers")->getAll(sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
        
        $values = array();
        $values[] = array("Exploitation - N° CVI",
                        "Exploitation - N° SIRET",
                        "Exploitation - Intitulé",
                        "Exploitation - Nom",
                        "Exploitation - Adresse",
                        "Exploitation - Code Postal",
                        "Exploitation - Commune",
                        "Exploitation - Téléphone",
                        "Exploitation - Fax",
                        "Exploitant - Sexe",
                        "Exploitant - Nom",
                        "Exploitant - Adresse",
                        "Exploitant - Code Postal",
                        "Exploitant - Commune",
                        "Exploitant - Naissance",
                        "Exploitant - Téléphone");
        foreach($tiers_ids as $id) {
            $data_revs = sfCouchdbManager::getClient("Tiers")->revs_info(true)->retrieveDocumentById($id, sfCouchdbClient::HYDRATE_JSON);
            if ($id != "TIERS-7523700100") {
                $revs = $data_revs->_revs_info;
                if (count($revs) > 2) {
                    $first_revision = $revs[count($revs)-3]->rev;
                } elseif(count($revs) > 1) {
                    $first_revision = $revs[count($revs)-2]->rev;
                } else {
                    $first_revision = $revs[count($revs)-1]->rev;
                }
                $tiers_old = sfCouchdbManager::getClient("Tiers")->rev($first_revision)->retrieveDocumentById($id, sfCouchdbClient::HYDRATE_ARRAY);
                $tiers_current = sfCouchdbManager::getClient("Tiers")->retrieveDocumentById($id, sfCouchdbClient::HYDRATE_ARRAY);
                if ($tiers_current->recoltant != 1) {
                    continue;
                }
                $values_changed = Tools::array_diff_recursive($tiers_current, $tiers_old);
                $value = array();
                $value[] = $this->formatModifiedValue(array('cvi' => true), $tiers_current, $values_changed);
                $value[] = $this->formatModifiedValue(array('siret' => true), $tiers_current, $values_changed);
                $value[] = $this->formatModifiedValue(array('intitule' => true), $tiers_current, $values_changed);
                $value[] = $this->formatModifiedValue(array('nom' => true), $tiers_current, $values_changed);
                $value[] = $this->formatModifiedValue(array('siege' => array('adresse' => true)), $tiers_current, $values_changed);
                $value[] = $this->formatModifiedValue(array('siege' => array('code_postal' => true)), $tiers_current, $values_changed);
                $value[] = $this->formatModifiedValue(array('siege' => array('commune' => true)), $tiers_current, $values_changed);
                $value[] = $this->formatModifiedValue(array('telephone' => true), $tiers_current, $values_changed);
                $value[] = $this->formatModifiedValue(array('fax' => true), $tiers_current, $values_changed);
                $value[] = $this->formatModifiedValue(array('exploitant' => array('sexe' => true)), $tiers_current, $values_changed);
                $value[] = $this->formatModifiedValue(array('exploitant' => array('nom' => true)), $tiers_current, $values_changed);
                $value[] = $this->formatModifiedValue(array('exploitant' => array('adresse' => true)), $tiers_current, $values_changed);
                $value[] = $this->formatModifiedValue(array('exploitant' => array('code_postal' => true)), $tiers_current, $values_changed);
                $value[] = $this->formatModifiedValue(array('exploitant' => array('commune' => true)), $tiers_current, $values_changed);
                $value[] = preg_replace('/(\d+)\-(\d+)\-(\d+)/', '\3/\2/\1', $this->formatModifiedValue(array('exploitant' => array('date_naissance' => true)), $tiers_current, $values_changed));
                $value[] = $this->formatModifiedValue(array('exploitant' => array('telephone' => true)), $tiers_current, $values_changed);

                $keys_used = array('cvi', 'siret', 'intitule', 'nom', 'siege', 'telephone', 'fax', 'exploitant');
                $nb_change = 0;
                foreach($keys_used as $key_use) {
                    if (array_key_exists($key_use, $values_changed)) {
                        $nb_change++;
                    }
                }

                if ($nb_change > 0) {
                    $values[] = $value;
                }
            }
        }
        
        $this->setResponseCsv('tiers-modifications.csv');
        return $this->renderText(Tools::getCsvFromArray($values));
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
