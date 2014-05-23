<?php

/**
 * upload actions.
 *
 * @package    civa
 * @subpackage upload
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class uploadActions extends EtapesActions {

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeMonEspaceCiva(sfWebRequest $request) {
        $this->help_popup_action = "help_popup_mon_espace_civa";
        $this->setCurrentEtape('mon_espace_civa');
        
        $this->formUploadCsv = new UploadCSVForm();

        if ($request->isMethod('post')) {
            $this->formUploadCsv->bind($request->getParameter($this->formUploadCsv->getName()), $request->getFiles($this->formUploadCsv->getName()));
            if ($this->formUploadCsv->isValid()) {
                return $this->redirect('upload/csvView?md5=' . $this->formUploadCsv->getValue('file')->getMd5());
            }
        }
        
        $this->setTemplate('monEspaceCiva', 'tiers');
    }
    
    public function executeCsvList(sfWebRequest $request) {
      $this->csv = acCouchdbManager::getClient('CSV')->getCSVsAcheteurs();
    }

    public function executeCsvDownload(sfWebRequest $request) {
        $this->forward404Unless($this->csv = acCouchdbManager::getClient("CSV")->retrieveByCviAndCampagne($this->getUser()->getTiers('Acheteur')->cvi));
        $this->setResponseCsv($this->csv->getCsvFilename());
        return $this->renderText(file_get_contents($this->csv->getAttachmentUri($this->csv->getCsvFilename())));
    }
    
    protected function setResponseCsv($filename) {
        $this->response->setContentType('application/csv');
        $this->response->setHttpHeader('Content-disposition', 'filename='.$filename, true);
        $this->response->setHttpHeader('Pragma', 'o-cache', true);
        $this->response->setHttpHeader('Expires', '0', true);
    }

    public static function cleanTable($table) {
        for ($i = 0; $i < count($table); $i++) {
            $table[$i] = preg_replace('/^\s+/', '', preg_replace('/\s+$/', '', $table[$i]));
        }
        return $table;
    }

    public function executeCsvView(sfWebRequest $request) {
        $md5 = $request->getParameter('md5');
        set_time_limit(600);

        $this->csv = new CsvFile(sfConfig::get('sf_data_dir') . '/upload/' . $md5);
        $this->cache = array();
        $cpt = -1;
        $this->errors = array();
        $this->warnings = array();
        $this->has_edel = 0;
        $this->nb_noVolumes = 0;
        $this->nb_cremant = 0;
        $this->nb_rebeche = 0;
	$this->productmd5 = array();

        if (isset($this->previous_recoltant))
            unset($this->previous_recoltant);
        foreach ($this->csv->getCsv() as $line) {
            $this->has_changed_recoltant = false;
            $line = self::cleanTable($line);
            $cpt++;
            $this->errors[$cpt] = array();
            $this->warnings[$cpt] = array();
            if (!$this->hasCVI($line)) {
                if (!$cpt)
                    continue;
                $this->errors[$cpt][] = 'Numero CVI invalide';
            }
            ;
            if ($this->shouldHaveRebeche($line)) {
                $this->errors[$cpt - 1][] = 'Ce recoltant produit du cremant, il devrait avoir des rebeches';
            }
            if ($this->errorOnCVIAcheteur($line)) {
                $this->errors[$cpt][] = 'Le CVI de la colonne acheteur ne correspond pas à celui de l\'utilisateur connecté';
            }
            if ($this->errorOnCVIRecoltant($line)) {
                $this->errors[$cpt][] = 'Le CVI de la colonne recoltant ne correspond pas à déclarant connu dans la base du CIVA.';
            }
            if ($errorprod = $this->cannotIdentifyProduct($line))
                $this->errors[$cpt][] = 'Il nous est impossible de repérer le produit correspondant à «' . $errorprod . '», merci de vérifier les libellés.';
            else {
	      if ($this->shouldHaveSuperficie($line))
                $this->errors[$cpt][] = 'La superficie est erronée.';
	      if ($this->cannotHaveDenomLieu($line)) {
                $this->errors[$cpt][] = 'Une dénomination géographique ou un lieu-dit ne peut être défini pour ce produit';
	      }
	    }
	    if ($this->shouldHaveDenomLieu($line)) {
	      $this->errors[$cpt][] = 'Un lieu-dit doit être défini pour ce produit';
	    }
            if (!$this->isVTSGNOk($line))
                $this->errors[$cpt][] = 'Le champ VT/SGN est non valide.';

	    if ($this->hasWrongUnit($line)) {
                $this->errors[$cpt][] = 'Les unités doivent être en ares et en hectolitres.';
	    }else if (!$this->hasGoodUnit($line)) {
                $this->warnings[$cpt][] = 'Les unités ne semblent pas être en ares et en hectolitres.';
            }
            if ($this->couldHaveSuperficie($line)) {
                $this->warnings[$cpt][] = 'La superficie pourrait être renseignée';
            }
            if ($this->cannotHaveRebeche($line)) {
                $this->errors[$cpt][] = 'Vous ne pouvez pas déclarer de rebeche. (Seule les caves peuvent le faire)';
            }
            if ($this->isVolumeNotCorrect($line))
                $this->errors[$cpt][] = 'Le volume n\'est pas correct';
            else if (!$this->hasVolume($line))
                $this->nb_noVolumes++;
	    if ($this->hasForbidenDenomination($line)) {
	      $this->errors[$cpt][] = 'La dénomination complémentaire utilisée n\'est pas autorisée';
	    }
	    if ($this->isProductAlreadyDefined($line)) {
	      $this->errors[$cpt][] = 'Ce produit semble déjà avoir été renseigné pour ce récoltant';
	    }
        }
        if ($this->shouldHaveRebeche(array())) {
            $this->errors[$cpt - 1][] = 'Ce recoltant produit du cremant, il devrait avoir des rebeches';
        }
        if ($this->shouldHaveVolume(array())) {
            $this->errors[$cpt - 1][] = 'Il existe des volumes sans surfaces alors qu\'aucun assemblage n\'a été déclaré';
        }
        $this->recap = new stdClass();
        $this->recap->errors = array();
        $this->recap->warnings = array();

        $nb_errors = 0;
        foreach ($this->errors as $line => $array) {
            foreach ($array as $value) {
                $nb_errors++;
                $this->recap->errors[$value][] = $line + 1;
            }
        }

        $nb_warnings = 0;
        foreach ($this->warnings as $line => $array) {
            foreach ($array as $value) {
                $nb_warnings++;
                $this->recap->warnings[$value][] = $line + 1;
            }
        }

        if (!$nb_errors) {
            $cvi = $this->getUser()->getTiers()->cvi;
            $csv = acCouchdbManager::getClient('CSV')->retrieveByCviAndCampagneOrCreateIt($cvi);
            $csv->storeCSV($this->csv);
            $csv->save();
            $this->getUser()->setFlash('confirmation', 'Les informations concernant la récolte de cette année ont bien été intégrées à notre base');
            if (!$nb_warnings)
                return $this->redirect('@mon_espace_civa_dr_acheteur');
        }
        if (!$this->csv) {
            $this->getUser()->setFlash('error', 'Le fichier fourni ne semble pas être un fichier CSV valide');
            return $this->redirect('@mon_espace_civa_dr_acheteur');
        }
    }

    protected function hasForbidenDenomination($line) {
      if ($line[CsvFile::CSV_DENOMINATION] && $line[CsvFile::CSV_LIEU] == $line[CsvFile::CSV_DENOMINATION])
	return true;
      if (preg_match('/VEND\.? TARD\.?/i', $line[CsvFile::CSV_DENOMINATION]))
	return true;
      if (preg_match('/vendenges? tardives?/i', $line[CsvFile::CSV_DENOMINATION]))
	return true;
      return false;
    }

    protected function isVTSGNOk($line) {
      if (!$line[CsvFile::CSV_VTSGN])
	return true;
      if ($line[CsvFile::CSV_VTSGN] && !$this->may_have_vtsgn) 
	return false;
      if ($line[CsvFile::CSV_VTSGN] == 'VT')
	return true;
      if ($line[CsvFile::CSV_VTSGN] == 'SGN')
	return true;
      return false;
    }

    protected function hasCVI($line) {
        if (preg_match('/^[67][578]\d{8}$/', $line[CsvFile::CSV_ACHETEUR_CVI]) && preg_match('/^[67][578]\d{8}$/', $line[CsvFile::CSV_RECOLTANT_CVI]))
            return true;
        return false;
    }

    protected function cannotHaveDenomLieu($line) {
        if ($this->has_lieudit && isset($line[CsvFile::CSV_LIEU]) && $line[CsvFile::CSV_LIEU])
            return false;
	if ($this->need_denomlieu && isset($line[CsvFile::CSV_LIEU]) && $line[CsvFile::CSV_LIEU])
	  return false;
	if (isset($line[CsvFile::CSV_LIEU]) && $line[CsvFile::CSV_LIEU])
	  return true;
	return false;
    }

    protected function isProductAlreadyDefined($line) {
      $md5 = md5($line[CsvFile::CSV_RECOLTANT_CVI].$line[CsvFile::CSV_APPELLATION].$line[CsvFile::CSV_LIEU].$line[CsvFile::CSV_CEPAGE].$line[CsvFile::CSV_VTSGN].$line[CsvFile::CSV_DENOMINATION]);
      if ($this->productmd5[$md5]) {
	return true;
      }
      $this->productmd5[$md5] = 1;
      return false;
    }

    protected function cannotIdentifyProduct($line) {
        $this->no_volume = false;
        $this->no_surface = false;
        $this->is_rebeche = false;
        $this->need_denomlieu = false;
        $this->has_lieudit = false;
	$this->may_have_vtsgn = false;

        if (!preg_match('/[a-z]/i', $line[CsvFile::CSV_APPELLATION])) {
            return "appellation vide";
        }

        if (strtolower($line[CsvFile::CSV_APPELLATION]) == 'jeunes vignes') {
            $this->no_volume = true;
            return false;
        }

        if (!preg_match('/[a-z]/i', $line[CsvFile::CSV_CEPAGE])) {
            return "cepage vide";
        }

        $prod = ConfigurationClient::getConfiguration()->identifyProduct($line[CsvFile::CSV_APPELLATION], $line[CsvFile::CSV_LIEU], $line[CsvFile::CSV_CEPAGE]);


        if (isset($prod['hash'])) {

            $cepage = ConfigurationClient::getConfiguration()->get($prod['hash']);
            $lieu = $cepage->getParent()->getParent();
            if ($lieu->getKey() != 'lieu')
                $this->has_lieudit = true;
            if ($lieu->getParent()->getParent()->hasLieuEditable() || preg_match('/_GRDCRU/', $prod['hash']))
                $this->need_denomlieu = true;

            if (preg_match('/_ED$/', $prod['hash'])) {
                $this->no_surface = true;
                $this->has_edel = 1;
            }

            if (preg_match('/_RB$/', $prod['hash'])) {
                $this->no_surface = true;
                $this->is_rebeche = true;
                $this->nb_rebeche++;
            }
            if (preg_match('/_CREMANT/', $prod['hash'])) {
                $this->nb_cremant++;
            }

	    $this->may_have_vtsgn = $cepage->hasVtsgn();
        }

        if (!isset($prod['error']))
            return false;
        return $prod['error'];
    }

    protected function shouldHaveDenomLieu($line) {
      if ($this->need_denomlieu && !$line[CsvFile::CSV_LIEU]) {
	return true;
      }
      return false;
    }

    protected function cannotHaveRebeche($line) {
        if (!$this->is_rebeche)
            return false;
        try {
            if ($this->getUser()->getTiers('Acheteur')->getQualite() != Acheteur::ACHETEUR_COOPERATIVE)
                return true;
        } catch (Exception $e) {
            return false;
        }
        return false;
    }

    private function hasChangedRecoltant($line) {
        if (!isset($line[CsvFile::CSV_RECOLTANT_CVI])) {
            return false;
        }
        if (!isset($this->previous_recoltant))
            $this->previous_recoltant = $line[CsvFile::CSV_RECOLTANT_CVI];
        if (isset($line[CsvFile::CSV_RECOLTANT_CVI]) && $line[CsvFile::CSV_RECOLTANT_CVI] == $this->previous_recoltant)
            return false;
        if ($this->has_changed_recoltant)
            return true;
        if (isset($line[CsvFile::CSV_RECOLTANT_CVI]))
            $this->previous_recoltant = $line[CsvFile::CSV_RECOLTANT_CVI];
        $this->has_changed_recoltant = true;
        return true;
    }

    private function shouldHaveVolume($line) {
        if (!$this->hasChangedRecoltant($line))
            return false;
        if (!$this->has_edel && $this->nb_noVolumes) {
            $this->nb_noVolumes = 0;
            return true;
        }
        $this->has_edel = 0;
        $this->nb_noVolumes = 0;
        return false;
    }

    private function shouldHaveRebeche($line) {
        try {
            if ($this->getUser()->getTiers('Acheteur')->getQualite() != Acheteur::ACHETEUR_COOPERATIVE)
                return false;
        } catch (Exception $e) {
            return false;
        }
        if (!$this->hasChangedRecoltant($line))
            return false;
        if ($this->nb_cremant && !$this->nb_rebeche) {
            $this->nb_cremant = 0;
            return true;
        }
        $this->nb_cremant = 0;
        $this->nb_rebeche = 0;
        return false;
    }

    private function isPositiveOrZero($val) {
        if (preg_match('/[^0-9\.\,]/', $val))
            return false;
        return $this->isPositive($val) || !$val;
    }

    private function isPositive($val) {
        $val = preg_replace('/,/', '.', $val);
        if (!is_numeric($val))
            return false;
        if (preg_match('/[^0-9\.\,]/', $val))
            return false;
        if ($val <= 0)
            return false;
        return true;
    }

    protected function hasWrongUnit($line) {
      if ($line[CsvFile::CSV_VOLUME] > 1000)
	return true;
      if ($line[CsvFile::CSV_SUPERFICIE] > 1000)
	return true;
      return false;
    }
    protected function hasGoodUnit($line) {
      if (
	  (preg_match('/^[0-9,\.]+$/', $line[CsvFile::CSV_SUPERFICIE]) || !$line[CsvFile::CSV_SUPERFICIE]) &&
	  (!$line[CsvFile::CSV_VOLUME] || preg_match('/^[0-9,\.]+$/', $line[CsvFile::CSV_VOLUME]))
	  )
            return true;
      if ($line[CsvFile::CSV_VOLUME] * 100 / $line[CsvFile::CSV_SUPERFICIE] > 10)
	return true;
      return false;
    }

    protected function hasVolume($line) {
        if ($this->no_volume)
            return true;
        return $this->isPositiveOrZero($line[CsvFile::CSV_VOLUME]);
    }

    protected function isVolumeNotCorrect($line) {
        if ($this->no_volume)
            return false;
        return!$this->isPositiveOrZero($line[CsvFile::CSV_VOLUME]);
    }

    protected function shouldHaveSuperficie($line) {
        if ($this->is_rebeche || $this->no_surface)
            return false;
        try {
            if (!isset($line[CsvFile::CSV_SUPERFICIE]) || !$line[CsvFile::CSV_SUPERFICIE])
                return ($this->getUser()->getTiers('Acheteur')->getQualite() != Acheteur::ACHETEUR_NEGOCIANT);
        } catch (Exception $e) {
            return false;
        }
        return!$this->isPositive($line[CsvFile::CSV_SUPERFICIE]);
    }

    protected function couldHaveSuperficie($line) {
        if ($this->is_rebeche || $this->no_surface)
            return false;
        try {
            if (!isset($line[CsvFile::CSV_SUPERFICIE]) || !$line[CsvFile::CSV_SUPERFICIE])
                return ($this->getUser()->getTiers('Acheteur')->getQualite() == Acheteur::ACHETEUR_NEGOCIANT);
        } catch (Exception $e) {
            return false;
        }
        return false;
    }

    protected function errorOnCVIAcheteur($line) {
        try {
            return ($this->getUser()->getTiers('Acheteur')->cvi != $line[CsvFile::CSV_ACHETEUR_CVI]);
        } catch (Exception $e) {
            return true;
        }
    }

    protected function errorOnCVIRecoltant($line) {
        if (!isset($this->cache[$line[CsvFile::CSV_RECOLTANT_CVI]])) {
            try {
                $rec = acCouchdbManager::getClient('Recoltant')->retrieveByCvi($line[CsvFile::CSV_RECOLTANT_CVI]);
                $this->cache[$line[CsvFile::CSV_RECOLTANT_CVI]] = !($rec);
            } catch (Exception $e) {
                $this->cache[$line[CsvFile::CSV_RECOLTANT_CVI]] = true;
            }
        }
        return $this->cache[$line[CsvFile::CSV_RECOLTANT_CVI]];
    }

}
