<?php

/**
 * admin actions.
 *
 * @package    civa
 * @subpackage admin
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class adminActions extends sfActions {

    /**
     *
     * @param sfRequest $request A request object
     */
    public function executeIndex(sfWebRequest $request) {
        $this->getUser()->signOutCompteUsed();
        $this->form = new AdminCompteLoginForm(null, array('comptes_type' => array('CompteTiers', 'CompteProxy')), false);
        $this->form_back_future = new AdminBackToTheFutureForm();
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->getUser()->signInCompteUsed($this->form->process());

                return $this->redirect('tiers', array('identifiant' => $this->form->getValue('login')));
            }
        }
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeGamma(sfWebRequest $request) {

        $this->forward404Unless($request->isMethod(sfWebRequest::POST));
        if ($request->getParameter('gamma_type_acces') == 'prod') {
            $this->redirect(sfConfig::get('app_gamma_url_prod'));
        } elseif ($request->getParameter('gamma_type_acces') == 'test') {
            $this->redirect(sfConfig::get('app_gamma_url_qualif'));
        }
    }

    /**
     *
     * @param sfRequest $request A request object
     */
    public function executeBackToFuture(sfWebRequest $request) {
        $this->form = new AdminBackToTheFutureForm();

        if (!$request->isMethod(sfWebRequest::POST)) {

            return $this->redirect('@admin');
        }

        $this->form->bind($request->getParameter($this->form->getName()));

        if (!$this->form->isValid()) {

            return $this->redirect('@admin');
        }

        $campagne = $this->form->getValue('campagne');

        $this->getUser()->setAttribute('back_to_the_future', $campagne);

        return $this->redirect('@admin');
    }

    public function executeBackToNow(sfWebRequest $request) {
        $this->getUser()->getAttributeHolder()->remove('back_to_the_future');

        return $this->redirect('@admin');
    }

    public function executeEtablissementDiff(sfWebRequest $request) {
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        $tiersCsv = new Db2Tiers2Csv(sfConfig::get('sf_root_dir')."/data/import/Tiers/Tiers-last");
        $this->keyIgnored = array(
                            EtablissementCsvFile::CSV_ID,
                            EtablissementCsvFile::CSV_TYPE,
                            EtablissementCsvFile::CSV_ID_SOCIETE,
                            EtablissementCsvFile::CSV_ID_COMPTE,
                            EtablissementCsvFile::CSV_NOM_COURT,
                            EtablissementCsvFile::CSV_ADRESSE_COMPLEMENTAIRE_1,
                            EtablissementCsvFile::CSV_ADRESSE_COMPLEMENTAIRE_2,
                            EtablissementCsvFile::CSV_RECETTE_LOCALE,
                            EtablissementCsvFile::CSV_CARTE_PRO,
                            EtablissementCsvFile::CSV_INSEE_DECLARATION,
                            EtablissementCsvFile::CSV_COMMUNE_DECLARATION,
                            EtablissementCsvFile::CSV_REGION,
                            EtablissementCsvFile::CSV_NATURE_INAO,
                            EtablissementCsvFile::CSV_INSEE,
                            EtablissementCsvFile::CSV_PAYS,
                            EtablissementCsvFile::CSV_TEL_PERSO,
                            EtablissementCsvFile::CSV_MOBILE,
                            EtablissementCsvFile::CSV_WEB,
                            EtablissementCsvFile::CSV_COMMENTAIRE,
                            EtablissementCsvFile::CSV_EXPLOITANT_PAYS,

                        );
        $this->etablissementsDb2 = $tiersCsv->getEtablissements();
        foreach($this->etablissementsDb2 as $id => $etablissement) {
            foreach($this->keyIgnored as $key) {
                $this->etablissementsDb2[$id][$key] = null;
            }
        }
        $this->etablissementsCouchdb = array();
        $results = EtablissementClient::getInstance()->startkey(array("INTERPRO-declaration"))
                            ->endkey(array("INTERPRO-declaration", array()))
            			    ->reduce(false)
            			    ->getView('etablissement', 'all');
        foreach($results->rows as $row) {
            $etablissement = EtablissementClient::getInstance()->find($row->id, acCouchdbClient::HYDRATE_JSON);
            if($etablissement->famille == "COURTIER") {
                continue;
            }

            $this->etablissementsCouchdb[$row->id] = EtablissementCsvFile::export($etablissement);

            if(isset($this->etablissementsCouchdb[$row->id]) && isset($this->etablissementsDb2[$row->id]) && $this->etablissementsCouchdb[$row->id][EtablissementCsvFile::CSV_STATUT] == $this->etablissementsDb2[$row->id][EtablissementCsvFile::CSV_STATUT] && $this->etablissementsCouchdb[$row->id][EtablissementCsvFile::CSV_STATUT] == EtablissementClient::STATUT_SUSPENDU) {
                unset($this->etablissementsDb2[$row->id]);
                unset($this->etablissementsCouchdb[$row->id]);
                continue;
            }

            if(isset($this->etablissementsDb2[$row->id])) {
                $this->etablissementsCouchdb[$row->id][5] = $this->etablissementsDb2[$row->id][5];
            }
            foreach($this->keyIgnored as $key) {
                $this->etablissementsCouchdb[$row->id][$key] = null;
            }
        }

        $this->setLayout('layout');
        $this->diff = array_diff_assoc_recursive($this->etablissementsDb2, $this->etablissementsCouchdb);
    }

    public function executeEtablissementDiffChargement(sfWebRequest $request) {
        $this->setLayout('layout');
    }
}

function array_diff_assoc_recursive($array1, $array2) {
    $difference=array();
    foreach($array1 as $key => $value) {
        if( is_array($value) ) {
            if( !isset($array2[$key]) || !is_array($array2[$key]) ) {
                $difference[$key] = $value;
            } else {
                $new_diff = array_diff_assoc_recursive($value, $array2[$key]);
                if( !empty($new_diff) )
                    $difference[$key] = $new_diff;
            }
        } else if( !array_key_exists($key,$array2) || $array2[$key] !== $value ) {
            $difference[$key] = $value;
        }
    }
    return $difference;
}
