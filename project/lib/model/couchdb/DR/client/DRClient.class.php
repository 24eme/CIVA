<?php

class DRClient extends acCouchdbClient {

  const AUTORISATION_ACHETEURS = 'ACHETEURS';
  const AUTORISATION_AVA = 'AVA';
  const VALIDEE_PAR_RECOLTANT = "RECOLTANT";
  const VALIDEE_PAR_CIVA = "CIVA";
  const VALIDEE_PAR_AUTO = "AUTO";
  const ACHETEUR_COOPERATIVE = 'Cooperative';
  const ACHETEUR_NEGOCIANT = 'Negociant';
  const ACHETEUR_NEGOCAVE = 'NegoCave';
  const ACHETEUR_RECOLTANT = 'Recoltant';

  protected $appellations_config_vtsgn = array();

  public static function getInstance() {

    return acCouchdbManager::getClient('DR');
  }

  public function createDeclaration($tiers, $campagne, $depot_mairie = false) {
    $doc = new DR();
    $doc->campagne = $campagne;
    $doc->cvi = $tiers->cvi;
    $this->initDeclaration($doc, $depot_mairie);

    return $doc;
  }

  public function createDeclarationClone($dr, $tiers, $campagne, $depot_mairie) {
    $doc = clone $dr;
    $doc->campagne = $campagne;
    $doc->cvi = $tiers->cvi;
    $this->initDeclaration($doc, $depot_mairie);
    $doc->devalidate();
    $doc->removeVolumes();
    $doc->remove('etape');
    $doc->remove('utilisateurs');
    $doc->remove('import_db2');
    $doc->update();

    return $doc;
  }

  protected function initDeclaration($doc, $depot_mairie = false) {
    $doc->constructId();
    $doc->storeDeclarant();
    $doc->remove('date_depot_mairie');
    if($depot_mairie){
      $doc->add('date_depot_mairie', null);
      $doc->addEtape('repartition');
    }
    $doc->add('lies_saisis_cepage', 1);
    $doc->add('jus_raisin_volume');
    $doc->add('jus_raisin_superficie');
  }

  public function hasImport($cvi, $campagne) {

    return acCouchdbManager::getClient('CSV')->countCSVsFromRecoltant($campagne, $cvi) > 0;
  }

  public function getAcheteursApporteur($cvi, $campagne) {
    $csv_ids = CSVClient::getInstance()->getCSVsFromRecoltantArray($campagne, $cvi);
    $acheteurs = array();
    foreach($csv_ids as $csv_id) {
        $cvi = preg_replace("/^CSV-([0-9]+)-.*/", '\1', $csv_id);
        $acheteurs[] = EtablissementClient::getInstance()->findByCvi($cvi);
    }

    return $acheteurs;
  }

    public function identifyProductCSV($line) {
        $appellation = $line[CsvFileAcheteur::CSV_APPELLATION];
        $appellation = preg_replace("/^0$/", "", $appellation);
        $appellation = preg_replace("/AOC ALSACE PINOT NOIR ROUGE/i", "AOC Alsace PN rouge", $appellation);

        $lieu = $line[CsvFileAcheteur::CSV_LIEU];
        $lieu = preg_replace("/^0$/", "", $lieu);

        $cepage = $line[CsvFileAcheteur::CSV_CEPAGE];
        $cepage = preg_replace("/^0$/", "", $cepage);
        $cepage = preg_replace("/Gewurzt\./i", "Gewurztraminer", $cepage);
        $cepage = preg_replace("/Muscat d'Alsace/i", "Muscat", $cepage);
        $cepage = preg_replace("/^Klevener/i", "Klevener de Heiligenstein ", $cepage);

        if(preg_match("/(AOC ALSACE PINOT NOIR|AOC ALSACE PN ROUGE)/i", $appellation)) {
            $cepage = null;
        }

        $vtsgn = $line[CsvFileAcheteur::CSV_VTSGN];
        $vtsgn = preg_replace("/^0$/", "", $vtsgn);

        $produit = ConfigurationClient::getConfiguration()->identifyProductByLibelle(trim(sprintf("%s %s %s %s", $appellation, $lieu, $cepage, $vtsgn)));

        if(!$produit) {
            $produit = ConfigurationClient::getConfiguration()->identifyProductByLibelle(trim(sprintf("%s %s %s", $appellation, $cepage, $vtsgn)));
        }

        return $produit;
    }

  public function createFromCSVRecoltant($campagne, $tiers, &$import, $depot_mairie = false) {
    $csvs = CSVClient::getInstance()->getCSVsFromRecoltant($campagne, $tiers->cvi);
    if (!$csvs || !count($csvs))
      throw new sfException('no csv found for '.$tiers->cvi) ;
    $campagne = $csvs[0]->campagne;

    $doc = $this->createDeclaration($tiers, $campagne, $depot_mairie);
    $doc->jeunes_vignes = 0;
    foreach ($csvs as $csv) {
          $acheteur_cvi = $csv->cvi;
          $acheteur_obj = EtablissementClient::getInstance()->findByCvi($csv->cvi);

          if (!$acheteur_obj)
        throw new sfException($acheteur_cvi.' acheteur inconnu');

          $import[] = $acheteur_obj;
          $linenum = 0;
          foreach ($csv->getCsvRecoltant($tiers->cvi) as $line) {
        $linenum++;
        if (preg_match('/JEUNES +VIGNES/i', $line[CsvFileAcheteur::CSV_APPELLATION])) {
          if($doc->jeunes_vignes == $this->recodeNumber($line[CsvFileAcheteur::CSV_SUPERFICIE])) {
            continue;
          }
          $doc->jeunes_vignes += $this->recodeNumber($line[CsvFileAcheteur::CSV_SUPERFICIE]);
          continue;
        }

        $produit = $this->identifyProductCSV($line);
        $prod = array();

        if($produit) {
            $prod["hash"] = $produit->getHash();
        } else {
            $prod = array("error" => $line[CsvFileAcheteur::CSV_APPELLATION].' '.$line[CsvFileAcheteur::CSV_LIEU].' '.$line[CsvFileAcheteur::CSV_CEPAGE]." ".$line[CsvFileAcheteur::CSV_VTSGN]);
        }

        if (!isset($prod['hash']))
          throw new sfException("Error on ".$prod['error']." (line $linenum / acheteur = $acheteur_cvi / recoltant = ".$tiers->cvi.')');

        $cepage = $doc->getOrAdd(HashMapper::inverse($prod['hash']));

        $denomlieu = '';
        if ($cepage->getLieu()->getKey() == 'lieu')
          $denomlieu = $line[CsvFileAcheteur::CSV_LIEU];

        if($denomlieu === "0") {
            $denomlieu = "";
        }
        $vtsgn = $line[CsvFileAcheteur::CSV_VTSGN];

        if($vtsgn === "0") {
            $vtsgn = "";
        }

        $denom = $line[CsvFileAcheteur::CSV_DENOMINATION];
        if($denom === "0") {
            $denom = "";
        }

        $detail = $cepage->retrieveDetailFromUniqueKeyOrCreateIt($denom, $vtsgn, $denomlieu);
        $detail->superficie += $this->recodeNumber($line[CsvFileAcheteur::CSV_SUPERFICIE]);
        $detail->volume += $this->recodeNumber($line[CsvFileAcheteur::CSV_VOLUME]);
        $detail->vci += $this->recodeNumber($line[CsvFileAcheteur::CSV_VOLUME_VCI]);
        if ($this->recodeNumber($line[CsvFileAcheteur::CSV_VOLUME]) == 0) {
          $detail->denomination = 'repli';
          $detail->add('motif_non_recolte', 'AE');
        }
          if($this->recodeNumber($line[CsvFileAcheteur::CSV_VOLUME]) > 0 || $this->recodeNumber($line[CsvFileAcheteur::CSV_SUPERFICIE]) > 0)
          {
              $acheteurDRType = "negoces";
              if ($acheteur_obj->acheteur_raisin == self::ACHETEUR_COOPERATIVE) {
                $acheteurDRType = "cooperatives";
              }
            $acheteurs = $detail->add($acheteurDRType);
            $acheteur = null;
            foreach ($acheteurs as $a) {
              if ($a->cvi == $acheteur_cvi)
                  $acheteur = $a;
                  break;
            }
            if (!$acheteur)
              $acheteur = $acheteurs->add();
            $acheteur->cvi = $acheteur_cvi;
            $acheteur->quantite_vendue += $this->recodeNumber($line[CsvFileAcheteur::CSV_VOLUME]);

            if($line[CsvFileAcheteur::CSV_VOLUME_VCI]) {
                $acheteurRecap = $detail->getCepage()->getNoeudRecapitulatif()->add('acheteurs')->get($acheteur->getParent()->getKey())->add($acheteur->cvi);
                $acheteurRecap->type_acheteur = $acheteur->getParent()->getKey();
                $acheteurRecap->dontvci = $acheteurRecap->dontvci + $line[CsvFileAcheteur::CSV_VOLUME_VCI];
            }
          }
        }
    }
    $doc->utilisateurs->edition->add('csv', date('d/m/Y'));
    $doc->update();

    return $doc;
  }

    public function getEtablissement($societe) {
        foreach($societe->getEtablissementsObject() as $etablissement) {

            if($etablissement->hasDroit(Roles::TELEDECLARATION_DR)) {

              return $etablissement;
            }
        }

        return null;
    }

    public function getEtablissementAcheteur($societe) {
        foreach($societe->getEtablissementsObject() as $etablissement) {

            if($etablissement->hasDroit(Roles::TELEDECLARATION_DR_ACHETEUR)) {

              return $etablissement;
            }
        }

        return null;
    }

    protected function recodeNumber($value) {

        return round(str_replace(",", ".", $value)*1, 2);
    }

    public function retrieveByCampagneAndCvi($cvi, $campagne, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {

        return $this->find('DR-'.$cvi.'-'.$campagne, $hydrate);
    }

    public function getAllByCampagne($campagne, $hydrate = acCouchdbClient::HYDRATE_ON_DEMAND) {
        $docs = $this->getAll($hydrate);
        $i = 0;
        $keys = array_keys($docs->getDocs());
        foreach($keys as $key) {
            if (substr($key, strlen($key) - 4, 4) != $campagne) {
                unset($docs[$key]);
            }
        }
        return $docs;
    }

    public function getAllByCvi($cvi, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
       return $this->startkey('DR-'.$cvi.'-0000')->endkey('DR-'.$cvi.'-2020')->execute($hydrate);
    }

    /*
     *
     * @param string $cvi
     * @param string $campagne
     * @return array
     */
    public function getArchivesSince($cvi, $campagne, $limit) {
        $docs = $this->startkey('DR-'.$cvi.'-0000')->endkey('DR-'.$cvi.'-'.$campagne)->execute(acCouchdbClient::HYDRATE_ON_DEMAND);
        $campagnes = array();
        foreach($docs->getIds() as $doc_id) {
            preg_match('/DR-(?P<cvi>\d+)-(?P<campagne>\d+)/', $doc_id, $matches);
            $campagnes[$doc_id] = $matches['campagne'];
        }
        krsort($campagnes);

        return array_slice($campagnes, 0, $limit);
    }

    public function getAll($hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {

        return $this->startkey('DR-0000000000-0000')->endkey('DR-9999999999-9999')->execute($hydrate);
    }

    public function findAllByCampagneAndCviAcheteur($campagne, $cvi_acheteur, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {

        return $this->startkey(array($campagne, $cvi_acheteur))->endkey(array($campagne, (string)($cvi_acheteur + 1)))->executeView("DR", "campagne_acheteur", $hydrate);
    }

    public function findAllByCampagneAcheteurs($campagne, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {

        return $this->startkey(array((string)$campagne))->endkey(array((string)($campagne+1)))->executeView("DR", "campagne_acheteur", $hydrate);
    }

    public function getTotauxByAppellationsRecap($dr) {
        $totauxByAppellationsRecap = array();

        $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap, 'ALSACEBLANC', null, "AOC Alsace Blanc");
        $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap, 'ALSACEROUGEROSE', null, "Rouge ou Rosé");
        $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap, 'GRDCRU', null, "AOC Alsace Grands Crus");
        $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap, 'CREMANT', null, "AOC Crémant d'Alsace");

          foreach ($dr->recolte->getAppellations() as $app_key => $appellation) {
              switch ($appellation_key = preg_replace('/^appellation_/', '', $app_key)) {
                  case 'GRDCRU':
                  case 'CREMANT':
                      $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap, $appellation_key, $appellation, $appellation->getLibelle());
                      break;
                  case 'ALSACEBLANC':
                  case 'COMMUNALE':
                  case 'LIEUDIT':
                  case 'PINOTNOIRROUGE':
                  case 'PINOTNOIR':
                      $totauxByAppellationsRecap = $this->getTotauxAgregeByCouleur($totauxByAppellationsRecap, $appellation_key, $appellation);
                      break;
              }
          }
        return $totauxByAppellationsRecap;
    }

    public function getTotauxAgregeByCouleur($totauxByAppellationsRecap, $app_key, $appellation) {
        foreach ($appellation->getLieux() as $lieu) {
            if (preg_match('/^PINOTNOIR/', $app_key)) {
                $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap, 'ALSACEROUGEROSE', $lieu, 'Rouge ou Rosé');
            } else if(!$appellation->getConfig()->existRendementCouleur()) {

                 $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap, 'ALSACEBLANC', $lieu, 'AOC Alsace Blanc');
            } else {
                  foreach ($lieu->getCouleurs() as $couleur_key => $couleur) {
                      if (preg_match('/Rouge$/', $couleur_key)) {
                          $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap, 'ALSACEROUGEROSE', $couleur, 'Rouge ou Rosé');
                      } else {
                          $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap, 'ALSACEBLANC', $couleur, 'AOC Alsace Blanc');
                      }
                  }
           }
        }
        return $totauxByAppellationsRecap;
    }

    public function getTotauxWithNode($totauxByAppellationsRecap, $key, $node, $nom) {
        if (!array_key_exists($key, $totauxByAppellationsRecap)) {
            $totauxByAppellationsRecap[$key] = new stdClass();
            $totauxByAppellationsRecap[$key]->nom = 'TOTAL ' . $nom;
            $totauxByAppellationsRecap[$key]->revendique_sur_place = null;
            $totauxByAppellationsRecap[$key]->usages_industriels_sur_place = null;
            $totauxByAppellationsRecap[$key]->vci_sur_place = null;
        }

        if (!$node) {

            return $totauxByAppellationsRecap;
        }

        $totauxByAppellationsRecap[$key]->revendique_sur_place += ($node->getVolumeRevendiqueCaveParticuliere()) ? $node->getVolumeRevendiqueCaveParticuliere() : 0;
        $totauxByAppellationsRecap[$key]->usages_industriels_sur_place += ($node->getUsagesIndustrielsSurPlace()) ? $node->getUsagesIndustrielsSurPlace() : 0;

        $totauxByAppellationsRecap[$key]->revendique_sur_place += $node->getTotalVolumeAcheteurs("mouts");
        $totauxByAppellationsRecap[$key]->vci_sur_place += $node->getVciCaveParticuliere();

        return $totauxByAppellationsRecap;
    }

    public function getDateOuverture() {

        return new DateTime(sfConfig::get('app_dr_date_ouverture'));
    }

    public function getDateFermeture() {

        return new DateTime(sfConfig::get('app_dr_date_fermeture'));
    }

    public function isTeledeclarationOuverte() {
        $dateOuverture = $this->getDateOuverture();
        $dateFermeture = $this->getDateFermeture();

        return date('Y-m-d') >= $dateOuverture->format('Y-m-d') && date('Y-m-d') <= $dateFermeture->format('Y-m-d');
    }

    public function getConfigAppellationsAvecVtsgn($configuration = null) {
        $appellations = array();

        if(!$configuration) {

            $configuration = ConfigurationClient::getCurrent();
        }

        if(array_key_exists($configuration->_id, $this->appellations_config_vtsgn)) {

            return $this->appellations_config_vtsgn[$configuration->_id];
        }

        foreach($configuration->declaration->getArrayAppellations() as $appellation) {
            if($appellation->getKey() == "PINOTNOIR") {
                $appellations["mentionVT"] = null;
                $appellations["mentionSGN"] = null;
            }


            if(!in_array($appellation->getCertification()->getKey(), array("AOC_ALSACE", "VINSSIG"))) {
                continue;
            }

            if($appellation->getAttribut('no_dr')) {
                continue;
            }

            if($appellation->getGenre()->getKey() == "VCI") {
                continue;
            }

            $appellations["appellation_".$appellation->getKey()] = null;
        }

        $appellations["mentionVT"] = array("libelle" => "Mention VT", "mout" => false, "hash" => "mentionVT", "lieux" => array());
        $appellations["mentionSGN"] = array("libelle" => "Mention SGN", "mout" => false, "hash" => "mentionSGN","lieux" => array());
        foreach($configuration->declaration->getArrayAppellations() as $appellation) {
            if($appellation->getGenre()->getKey() == "VCI") {
                continue;
            }
            if(!array_key_exists("appellation_".$appellation->getKey(), $appellations)) {
                continue;
            }
            $hash = HashMapper::inverse($appellation->getHash());
            $appellations["appellation_".$appellation->getKey()] = array("libelle" => $appellation->getLibelle(), "mout" => $appellation->exist('attributs/mout') && $appellation->attributs->mout, "hash" => $hash."/mention", "lieux" => array());
            foreach($appellation->getMentions() as $mention) {
                if($mention->getKey() != "DEFAUT") {
                    $hash = "mention".$mention->getKey();
                }
                if($mention->hasManyLieu() || $mention->getKey() != "DEFAUT") {
                    foreach($mention->getLieux() as $lieu)  {
                        $appellations[($mention->getKey() == "DEFAUT") ? "appellation_".$appellation->getKey() : "mention".$mention->getKey()]['lieux'][HashMapper::inverse($lieu->getHash())] = $lieu;
                    }
                }
            }

        }

        $this->appellations_config_vtsgn[$configuration->_id] = $appellations;

        return $this->appellations_config_vtsgn[$configuration->_id];
    }

}
