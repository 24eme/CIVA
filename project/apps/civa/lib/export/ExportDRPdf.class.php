<?php

class ExportDRPdf extends ExportDocument {
    protected $type;
    protected $document;
    protected $nb_pages;
    protected $partial_function;
    protected $file_dir;
    protected $no_cache;
    protected $filename;
    protected $dr;

    public function __construct($dr, $partial_function, $type = 'pdf', $file_dir = null, $no_cache = false, $filename = null) {
        $this->type = $type;
        $this->partial_function = $partial_function;
        $this->file_dir = $file_dir;
        $this->no_cache = $no_cache;
        $this->dr = $dr;

        $this->init($dr, $filename);
    }

    public function generatePDF() {
        if($this->no_cache || !$this->isCached()) {
          $this->create($this->dr);
        }
        return $this->document->generatePDF($this->no_cache);
    }

    protected function getLibelleValidation($dr) {
        $libelle = "";

        if (!$dr->isValideeTiers()) {

          return "Exemplaire brouillon";
        }

        if($dr->hasDateDepotMairie()){

            return 'Déposée en mairie le '.$dr->getDateDepotMairieFr() . $this->getLibelleModification($dr);
        }

        $libelle = "Déclaration validée le ".$dr->getDateValideeFr();

        if($dr->exist('validee_par') && $dr->validee_par) {

            $libelle .= $this->getLibelleValidationPar($dr->validee_par);
        }

        return $libelle . $this->getLibelleModification($dr);
    }

    protected function getLibelleModification($dr) {
        $libelle = null;

        if ($dr->isHumanlyModifiee()) {
            $libelle .= ' et modifiée le '.$dr->getDateModifieeFr();
        }

        if ($dr->isHumanlyModifiee() && $dr->exist('modifiee_par') && $dr->modifiee_par) {
            $libelle .= $this->getLibelleValidationPar($dr->modifiee_par);
        }

        return $libelle;
    }

    protected function getLibelleValidationPar($par) {
        if($par == DRClient::VALIDEE_PAR_RECOLTANT) {

            return " par le récoltant";
        }

        if($par == DRClient::VALIDEE_PAR_CIVA) {

            return " par le CIVA";
        }

        if($par == DRClient::VALIDEE_PAR_AUTO) {

            return " automatiquement";
        }

        return " par ". $par;
    }

    protected function init($dr, $filename = null) {
        $validee = $this->getLibelleValidation($dr);

        $title = 'Déclaration de récolte '.$dr->campagne;
        $header = $dr->declarant->nom."\nCommune de déclaration : ".$dr->declaration_commune."\n".$validee;
        if (!$filename) {
            $filename = $this->getFileName(true, true);
        }

        if ($this->type == 'html') {
          $this->document = new PageableHTML($title, $header, $filename, $this->file_dir);
        }else {
          $this->document = new PageablePDF($title, $header, $filename, $this->file_dir);
        }
    }

    public function getFileName($with_name = true, $with_rev = false) {

      return self::buildFileName($this->dr, $with_name, $with_rev);
    }

    public static function buildFileName($dr, $with_name = true, $with_rev = false) {
      $filename = sprintf("DR_%s_%s", $dr->cvi, $dr->campagne);

      if($with_name) {
          $declarant_nom = strtoupper(KeyInflector::slugify($dr->declarant->nom));
          $filename .= '_'.$declarant_nom;
      }

      if($with_rev) {
            $filename .= '_'.$dr->_rev;
      }

      return $filename.'.pdf';
    }

    protected function create($dr) {
        foreach ($dr->getAppellationsAvecVtsgn() as $appellation_key => $appellation) {
            if(in_array($appellation_key, array("mentionVT", "mentionSGN"))) {
                $this->createAppellationLieu($appellation['lieux'],
                                             true,
                                             $appellation["libelle"],
                                             str_replace("mention", "", $appellation_key));
                continue;
            }

            foreach ($appellation['lieux'] as $lieu) {
                $this->createAppellationLieu(array($lieu),
                                             $lieu->getConfig()->hasVtsgn(),
                                             $lieu->getLibelleWithAppellation());
            }

            if(!count($appellation['lieux'])) {
                $lieu = $dr->get($appellation['hash']."/lieu");
                $this->createAppellationLieu(array($lieu),
                                            $lieu->getConfig()->hasVtsgn(),
                                            $lieu->getLibelleWithAppellation());
            }
        }

      $infos = $this->getRecapitulatifInfos($dr);

      $infosPage = array();
	  $nb_colonnes_by_page = 6;
          $nb_colonnes = count($infos['appellations']) - 1;
	  if ($nb_colonnes == 5)
		$nb_colonnes_by_page = 5;
          if ($nb_colonnes >= $nb_colonnes_by_page) {
			$pages = array();
    		for ($i = 0 ; $i <= $nb_colonnes; ) {
      			$page = array_slice($infos['appellations'], $i, $nb_colonnes_by_page);
      			$infosPage[] = array(
      				'appellations' => $page,
      				'libelle' => array_slice($infos['libelle'], $i, $nb_colonnes_by_page),
      				'superficie' => array_slice($infos['superficie'], $i, $nb_colonnes_by_page),
      				'volume' => array_slice($infos['volume'], $i, $nb_colonnes_by_page),
                    'volume_vendus' => array_slice($infos['volume_vendus'], $i, $nb_colonnes_by_page),
                    'volume_sur_place' => array_slice($infos['volume_sur_place'], $i, $nb_colonnes_by_page),
                    'volume_rebeches' => array_slice($infos['volume_rebeches'], $i, $nb_colonnes_by_page),
                    'volume_rebeches_sur_place' => array_slice($infos['volume_rebeches_sur_place'], $i, $nb_colonnes_by_page),
                    'revendique' => array_slice($infos['revendique'], $i, $nb_colonnes_by_page),
                    'revendique_sur_place' => array_slice($infos['revendique_sur_place'], $i, $nb_colonnes_by_page),
                    'usages_industriels' => array_slice($infos['usages_industriels'], $i, $nb_colonnes_by_page),
      				'usages_industriels_sur_place' => array_slice($infos['usages_industriels_sur_place'], $i, $nb_colonnes_by_page),
      				'volume_vci' => array_slice($infos['volume_vci'], $i, $nb_colonnes_by_page),
      				'volume_vci_sur_place' => array_slice($infos['volume_vci_sur_place'], $i, $nb_colonnes_by_page),
      				'total_superficie' => $infos['total_superficie'],
                    'total_volume' => $infos['total_volume'],
        			'total_volume_vendus' => $infos['total_volume_vendus'],
                    'total_usages_industriels' => $infos['total_usages_industriels'],
                    'total_usages_industriels_sur_place' => $infos['total_usages_industriels_sur_place'],
                    'total_revendique' => $infos['total_revendique'],
        			'total_revendique_sur_place' => $infos['total_revendique_sur_place'],
                    'total_volume_sur_place' => $infos['total_volume_sur_place'],
                    'total_volume_rebeches' => $infos['total_volume_rebeches'],
                    'total_volume_rebeches_sur_place' => $infos['total_volume_rebeches_sur_place'],
                    'total_volume_vci' => $infos['total_volume_vci'],
                    'total_volume_vci_sur_place' => $infos['total_volume_vci_sur_place'],
                    'lies' => $infos['lies'],
        			'jeunes_vignes' => $infos['jeunes_vignes'],

      			);
      			$i += count($page);
      			$pages[] = $page;
    		}
    		$nb_pages = count($pages);
    		$currentPage = 1;
    		foreach ($pages as $key => $page) {
    			if ($currentPage == $nb_pages)
    				$has_total = true;
    			else
    				$has_total = false;
    			$this->document->addPage($this->getPartial('dr_export/recapitulatif', array('dr'=> $this->dr, 'infos'=> $infosPage[$key], 'has_total' => $has_total, 'has_no_usages_industriels' => $dr->recolte->getConfig()->hasNoUsagesIndustriels())));
    			$currentPage++;
    		}

          } else {
          	$this->document->addPage($this->getPartial('dr_export/recapitulatif', array('dr'=> $this->dr, 'infos'=> $infos, 'has_total' => true, 'has_no_usages_industriels' => $dr->recolte->getConfig()->hasNoUsagesIndustriels())));
          }
          if(!$dr->recolte->getConfig()->hasNoUsagesIndustriels() && !$dr->recolte->getConfig()->hasNoRecapitulatifCouleur()) {
            $this->createRecap($dr);
          }
    }

      protected function createRecap($dr) {
        $recap = $this->getRecapTotal($dr);
        $total = array("revendique_sur_place" => null,
                       "usages_industriels_sur_place" => null,
                       "dplc_sur_place_rouge" => null,
                       "dplc_sur_place_blanc" => null,
                       "vci_sur_place" => null);
        foreach($recap as $key => $item) {
            $total["revendique_sur_place"] += $item->revendique_sur_place;
            $total["usages_industriels_sur_place"] += $item->usages_industriels_sur_place;
            $total["dplc_sur_place_rouge"] += $item->dplc_sur_place_rouge;
            $total["dplc_sur_place_blanc"] += $item->dplc_sur_place_blanc;
            $total["vci_sur_place"] += $item->vci_sur_place;
        }
        if($dr->hasVolumeSurPlace() && !$dr->recolte->getConfig()->hasNoRecapitulatifCouleur()){
          $this->document->addPage($this->getPartial('dr_export/recapitulatifDRM', array('dr' => $dr,
                                                                                      'recap_total' => $recap,
                                                                                      'total' => $total)));
        }
    }

    protected function getRecapTotal($dr) {

        return DRClient::getInstance()->getTotauxByAppellationsRecap($dr);
    }


    private function getRecapitulatifInfos($dr)
    {
        $appellations = array();
        $superficie = array();
        $volume = array();
        $volume_vendus = array();
        $volume_sur_place = array();
        $volume_rebeches = array();
        $volume_rebeches_sur_place = array();
        $revendique = array();
        $revendique_sur_place = array();
        $usages_industriels = array();
        $usages_industriels_sur_place = array();
        $volume_vci = array();
        $volume_vci_sur_place = array();
        $libelle = array();
        $volume_negoces = array();
        $volume_cooperatives = array();
        $cvi = array();
        $has_cepage_rb = false;

        foreach ($dr->getAppellationsAvecVtsgn() as $appellation) {
            $appellations[] = $appellation["hash"];
            $libelle[$appellation["hash"]] = $appellation['libelle'];
            $superficie[$appellation["hash"]] = 0;
            $volume[$appellation["hash"]] = 0;
            $volume_vendus[$appellation["hash"]] = 0;
            $revendique[$appellation["hash"]] = 0;
            $revendique_sur_place[$appellation["hash"]] = 0;
            $usages_industriels[$appellation["hash"]] = 0;
            $usages_industriels_sur_place[$appellation["hash"]] = 0;
            $volume_sur_place[$appellation["hash"]] = 0;
            $volume_rebeches[$appellation["hash"]] = null;
            $volume_rebeches_sur_place[$appellation["hash"]] = null;
            $volume_vci[$appellation["hash"]] = 0;
            $volume_vci_sur_place[$appellation["hash"]] = 0;

            foreach($appellation["noeuds"] as $noeud) {
                if ($noeud->getConfig()->excludeTotal()) {
                    continue;
                }
                $superficie[$appellation["hash"]] += $noeud->getTotalSuperficie();
                $volume[$appellation["hash"]] += $noeud->getTotalVolume();
                $volume_vendus[$appellation["hash"]] += $noeud->getTotalVolumeVendus();
                $revendique[$appellation["hash"]] += $noeud->getVolumeRevendique();
                $revendique_sur_place[$appellation["hash"]] += $noeud->getVolumeRevendiqueCaveParticuliere();
                $usages_industriels_sur_place[$appellation["hash"]] += $noeud->getUsagesIndustrielsSurPlace();
                $usages_industriels[$appellation["hash"]] += $noeud->getUsagesIndustriels();
                $volume_sur_place[$appellation["hash"]] += $noeud->getTotalCaveParticuliere();
                if($noeud->getConfig()->hasCepageRB()) {
                    $volume_rebeches[$appellation["hash"]] += $noeud->getTotalRebeches();
                    $volume_rebeches_sur_place[$appellation["hash"]] += $noeud->getSurPlaceRebeches();
                }
                $volume_vci[$appellation["hash"]] += $noeud->getTotalVci();
                $volume_vci_sur_place[$appellation["hash"]] += $noeud->getVciCaveParticuliere();

                if($noeud->getConfig()->hasCepageRB()) {
                    $has_cepage_rb = true;
                }
            }
        }

        $infos = array();
        $infos['appellations'] = $appellations;
        $infos['libelle'] = $libelle;
        $infos['superficie'] = $superficie;
        $infos['volume'] = $volume;
        $infos['volume_vendus'] = $volume_vendus;
        $infos['volume_sur_place'] = $volume_sur_place;
        $infos['volume_rebeches'] = $volume_rebeches;
        $infos['volume_rebeches_sur_place'] = $volume_rebeches_sur_place;
        $infos['volume_vci'] = $volume_vci;
        $infos['volume_vci_sur_place'] = $volume_vci_sur_place;
        $infos['revendique'] = $revendique;
        $infos['revendique_sur_place'] = $revendique_sur_place;
        $infos['usages_industriels'] = $usages_industriels;
        $infos['usages_industriels_sur_place'] = $usages_industriels_sur_place;
        $infos['total_superficie'] = array_sum(array_values($superficie));
        $infos['total_volume'] = array_sum(array_values($volume));

        $has_no_usages_industriels = $dr->recolte->getConfig()->hasNoUsagesIndustriels();
        $has_no_recapitulatif_couleur = $dr->recolte->getConfig()->hasNoRecapitulatifCouleur();
        $can_calcul_volume_revendique_sur_place = $this->dr->recolte->canCalculVolumeRevendiqueSurPlace();

        if($dr->recolte->getTotalVolumeVendus() > 0 && !$has_no_usages_industriels && !$has_no_recapitulatif_couleur) {
          $infos['total_volume_vendus'] = array_sum(array_values($volume_vendus));
        } else {
          $infos['total_volume_vendus'] = null;
        }

        $infos['total_volume_sur_place'] = array_sum(array_values($volume_sur_place));

        if($has_cepage_rb && !$has_no_usages_industriels && !$has_no_recapitulatif_couleur) {
            $infos['total_volume_rebeches'] = array_sum(array_values($volume_rebeches));
            $infos['total_volume_rebeches_sur_place'] = array_sum(array_values($volume_rebeches_sur_place));
        } else {
          $infos['total_volume_rebeches'] = null;
          $infos['total_volume_rebeches_sur_place'] = null;
        }

        $infos['total_usages_industriels'] = array_sum(array_values($usages_industriels));

        if($dr->recolte->getTotalVolumeVendus() > 0 && $can_calcul_volume_revendique_sur_place && !$has_no_usages_industriels && !$has_no_recapitulatif_couleur) {
          $infos['total_usages_industriels_sur_place'] = array_sum(array_values($usages_industriels_sur_place));
        } else {
          $infos['total_usages_industriels_sur_place'] = null;
        }

        $infos['total_revendique'] = array_sum(array_values($revendique));

        if($dr->recolte->getTotalVolumeVendus() > 0 && $can_calcul_volume_revendique_sur_place && !$has_no_usages_industriels && !$has_no_recapitulatif_couleur) {
          $infos['total_revendique_sur_place'] = array_sum(array_values($revendique_sur_place));
        } else {
          $infos['total_revendique_sur_place'] = null;
        }

        $infos['total_volume_vci'] = null;
        $infos['total_volume_vci_sur_place'] = null;
        if($dr->recolte->canHaveVci()) {
            $infos['total_volume_vci'] = array_sum(array_values($volume_vci));
        }
        if($dr->recolte->canHaveVci() && $dr->recolte->getTotalVolumeVendus() > 0 && $can_calcul_volume_revendique_sur_place) {
            $infos['total_volume_vci_sur_place'] = array_sum(array_values($volume_vci_sur_place));
        }

        $infos['jeunes_vignes'] = $dr->jeunes_vignes;
        $infos['lies'] = $dr->lies;
        return $infos;
    }

	private function createAppellationLieu($lieux, $hasVTSGN, $libelle, $totalMentionLibelle = null) {
        $colonnes = array();
        $afterTotal = array();
        $hasLigneLieu = false;
        $hasLigneAppellation = false;
        $acheteurs = array();
        $acheteursLignes = array();

        if($totalMentionLibelle) {
            $colonneTotalMention = array();
            $colonneTotalMention['type'] = 'total';
            $colonneTotalMention['appellation'] = "Total";
            $colonneTotalMention['lieu'] = "";
            $colonneTotalMention['cepage'] = "";
            $colonneTotalMention['denomination'] = "Mention";
            $colonneTotalMention['vtsgn'] = $totalMentionLibelle;
            $colonneTotalMention['superficie'] = 0;
            $colonneTotalMention['volume'] = 0;
            $colonneTotalMention['cave_particuliere'] = 0;
            $colonneTotalMention['revendique'] = 0;
            $colonneTotalMention['usages_industriels'] = 0;
            $colonneTotalMention['vci'] = 0;
        }

        foreach($lieux as $lieu) {
            if(!$hasLigneLieu && ($lieu->getConfig()->hasLieuEditable() || $lieu->getMention()->getKey() != "mention")) {
                $hasLigneLieu = true;
            }
            if(!$hasLigneAppellation && $lieu->getMention()->getKey() != "mention") {
                $hasLigneAppellation = true;
            }
            $hasManyCouleur = count($lieu->getConfig()->getCouleurs()) > 1;
            if($lieu->hasAcheteurs()) {
                $acheteurs[$lieu->getHash()] = array("libelle_appellation" => $lieu->getAppellation()->getLibelleCourt() . (($lieu->getLibelle()) ? " - " . $lieu->getLibelle() : null), "acheteurs" => $lieu->acheteurs);
                foreach($lieu->acheteurs as $type_key => $acheteursType) {
                    foreach($acheteursType as $cvi => $acheteur) {
                        $type = 'Vente à ';
                        if ($acheteur->type_acheteur == 'cooperatives') {
                        $type = 'Apport à ';
                        } else if ($acheteur->type_acheteur == 'mouts') {
                        $type = 'Vente de mouts à ';
                        }
                        $acheteursLignes[$type_key.'_'.$cvi] = $type.$acheteur->nom;
                    }
                }
            }
            $cpt = 0;
            foreach ($lieu->getCouleurs() as $couleur) {
                $nbCepageCouleur = 0;
                foreach ($couleur->getConfig()->getCepages() as $cepage_config) {
                    if (!$couleur->getDocument()->exist(HashMapper::inverse($cepage_config->getHash()))) {
                        continue;
                    }
                    $cepage = $couleur->getDocument()->get(HashMapper::inverse($cepage_config->getHash()));

                    if (!count($cepage->detail)) {

                        continue;
                    }

                    $i = 0;
                    foreach ($cepage->detail as $detail) {
                        $c = array();
                        $c['type'] = 'detail';
                        if($lieu->getMention()->getKey() != 'mention') {
                            $c['appellation'] = $lieu->getAppellation()->getLibelleCourt();
                            $c['lieu'] = $lieu->getLibelle();
                        }
                        $c['cepage'] = $cepage->getLibelle();
                        $c['denomination'] = $detail->denomination;
                        $c['vtsgn'] = $detail->vtsgn;
                        if($lieu->getMention()->getKey() != "mention") {
                            $c['vtsgn'] = $lieu->getMention()->getLibelle();
                        }
                        $c['superficie'] = $detail->superficie;
                        $c['volume'] = $detail->volume;
                        if($detail->canHaveUsagesLiesSaisi()) {
                            $c['revendique'] = $detail->volume_revendique;
                            $c['usages_industriels'] = $detail->lies;
                        }

                        $c['vci'] = $detail->vci;

                        if ($lieu->getConfig()->hasLieuEditable()) {
                            $c['lieu'] = $detail->lieu;
                        }

                        if ($detail->hasMotifNonRecolteLibelle() && $detail->motif_non_recolte && !in_array($detail->motif_non_recolte, array('AE', 'DC'))) {
                            $c['motif_non_recolte'] = $detail->getMotifNonRecolteLibelle();
                        }
                        $c['cave_particuliere'] = $detail->cave_particuliere;
                        foreach($detail->negoces as $vente) {
                            $c['negoces_'.$vente->cvi] = $vente->quantite_vendue;
                        }
                        foreach($detail->cooperatives as $vente) {
                            $c['cooperatives_'.$vente->cvi] = $vente->quantite_vendue;
                        }
                        if ($detail->exist('mouts')) {
                            foreach($detail->mouts as $vente) {
                                $c['mouts_'.$vente->cvi] = $vente->quantite_vendue;
                            }
                        }

                        if ($cepage->getConfig()->excludeTotal()) {
                            array_push($afterTotal, $c);
                        }else{
                            $last = array_push($colonnes, $c) - 1;
                        }
                        $i++;
                        $cpt ++;
                    } // endforeach; details des cepages
                    if ($cepage->getConfig()->hasTotalCepage()) {
                        if ($i > 1) {
                            $c = array();
                            $c['type'] = 'total';
                            if($lieu->getMention()->getKey() != 'mention') {
                                $c['appellation'] = $lieu->getAppellation()->getLibelleCourt();
                                $c['lieu'] = $lieu->getLibelle();
                            }
                            $c['cepage'] = $cepage->getLibelle();
                            $c['denomination'] = 'Total';
                            $c['vtsgn'] = '';
                            if($lieu->getMention()->getKey() != "mention") {
                                $c['vtsgn'] = $lieu->getMention()->getLibelle();
                            }
                            $c['superficie'] = $cepage->total_superficie;
                            $c['volume'] = $cepage->total_volume;
                            $c['cave_particuliere'] = $cepage->getTotalCaveParticuliere();
                            $c['revendique'] = $cepage->volume_revendique;
                            $c['usages_industriels'] = $cepage->usages_industriels;
                            if (!$c['usages_industriels']) {
                                $c['usages_industriels'] = '0,00';
                            }
                            $c['vci'] = $cepage->getTotalVci();
                            $negoces = $cepage->getVolumeAcheteurs('negoces');
                            foreach($negoces as $cvi => $total) {
                                $c['negoces_'.$cvi] = $total;
                            }
                            $coop =  $cepage->getVolumeAcheteurs('cooperatives');
                            foreach($coop as $cvi => $total) {
                                $c['cooperatives_'.$cvi] = $total;
                            }
                            $mouts =  $cepage->getVolumeAcheteurs('mouts');
                            foreach($mouts as $cvi => $total) {
                                $c['mouts_'.$cvi] = $total;
                            }
                            array_push($colonnes, $c);
                            $cpt ++;
                        }else{
                            $colonnes[$last]['type'] = 'total';
                            $colonnes[$last]['revendique'] = $cepage->volume_revendique;
                            $colonnes[$last]['usages_industriels'] = $cepage->usages_industriels;
                            if (!$colonnes[$last]['usages_industriels'])
                            $colonnes[$last]['usages_industriels'] = '0,00';
                        }
                    }
                    $nbCepageCouleur++;
                } // endforeach; cepages
                if ($hasManyCouleur && $nbCepageCouleur > 0) {
                    $c = array();
                    $c['type'] = 'total';
                    if($lieu->getMention()->getKey() != 'mention') {
                        $c['appellation'] = $lieu->getAppellation()->getLibelleCourt();
                        $c['lieu'] = $lieu->getLibelle();
                    }
                    $c['cepage'] = 'Total';
                    if ($lieu->getConfig()->hasLieuEditable())
                    $c['lieu'] = $couleur->libelle;
                    else
                    $c['denomination'] = $couleur->libelle;
                    $c['vtsgn'] = '';
                    if($lieu->getMention()->getKey() != "mention") {
                        $c['vtsgn'] = $lieu->getMention()->getLibelle();
                    }
                    $c['superficie'] = $couleur->total_superficie;
                    $c['volume'] = $couleur->total_volume;
                    $c['cave_particuliere'] = $couleur->getTotalCaveParticuliere();
                    $c['revendique'] = $couleur->volume_revendique;
                    $c['usages_industriels'] = $couleur->usages_industriels;
                    if (!$c['usages_industriels']) {
                        $c['usages_industriels'] = '0,00';
                    }
                    $c['vci'] = $couleur->getTotalVci();
                    $negoces = $couleur->getVolumeAcheteurs('negoces');
                    foreach($negoces as $cvi => $vente) {
                        $c['negoces_'.$cvi] = $vente;
                    }
                    $coop =  $couleur->getVolumeAcheteurs('cooperatives');
                    foreach($coop as $cvi => $vente) {
                        $c['cooperatives_'.$cvi] = $vente;
                    }
                    $mouts =  $couleur->getVolumeAcheteurs('mouts');
                    foreach($mouts as $cvi => $vente) {
                        $c['mouts_'.$cvi] = $vente;
                    }
                    array_push($colonnes, $c);
                }
            } // endforeach; couleurs
            $c = array();
            $c['type'] = 'total';
            if($lieu->getMention()->getKey() != 'mention') {
                $c['appellation'] = $lieu->getAppellation()->getLibelleCourt();
                $c['lieu'] = $lieu->getLibelle();
            }
            $c['cepage'] = 'Total';
            $c['denomination'] = ($lieu->getKey() == 'lieu') ? 'Appellation' : '';
            if ($lieu->getAppellation()->getAppellation() == 'VINTABLE')
            $c['denomination'] = '';
            if($lieu->getMention()->getKey() != "mention") {
                $c['denomination'] = "";
                $c['vtsgn'] = $lieu->getMention()->getLibelle();
            }
            $c['superficie'] = $lieu->total_superficie;
            if(isset($colonneTotalMention)) { $colonneTotalMention['superficie'] += $c['superficie']; }
            $c['volume'] = $lieu->total_volume;
            if(isset($colonneTotalMention)) { $colonneTotalMention['volume'] += $c['volume']; }
            $c['cave_particuliere'] = $lieu->getTotalCaveParticuliere();
            if(isset($colonneTotalMention)) { $colonneTotalMention['cave_particuliere'] += $c['cave_particuliere']; }
            $c['revendique'] = $lieu->volume_revendique;
            if(isset($colonneTotalMention)) { $colonneTotalMention['revendique'] += $c['revendique']; }
            $c['usages_industriels'] = $lieu->usages_industriels;
            if(isset($colonneTotalMention)) { $colonneTotalMention['usages_industriels'] += $c['usages_industriels']; }
            if (!$c['usages_industriels']) {
                $c['usages_industriels'] = '0,00';
            }
            $c['vci'] = $lieu->getTotalVci();
            if(isset($colonneTotalMention)) { $colonneTotalMention['vci'] += $c['vci']; }

            $negoces = $lieu->getVolumeAcheteurs('negoces');
            foreach($negoces as $cvi => $vente) {
                $c['negoces_'.$cvi] = $vente;
                if(isset($colonneTotalMention) && !isset($colonneTotalMention['negoces_'.$cvi])) { $colonneTotalMention['negoces_'.$cvi] = 0; }
                if(isset($colonneTotalMention)) { $colonneTotalMention['negoces_'.$cvi] += $c['negoces_'.$cvi]; }
            }
            $coop =  $lieu->getVolumeAcheteurs('cooperatives');
            foreach($coop as $cvi => $vente) {
                $c['cooperatives_'.$cvi] = $vente;
                if(isset($colonneTotalMention) && !isset($colonneTotalMention['cooperatives_'.$cvi])) { $colonneTotalMention['cooperatives_'.$cvi] = 0; }
                if(isset($colonneTotalMention)) { $colonneTotalMention['cooperatives_'.$cvi] += $c['cooperatives_'.$cvi]; }
            }
            $mouts =  $lieu->getVolumeAcheteurs('mouts');
            foreach($mouts as $cvi => $vente) {
                $c['mouts_'.$cvi] = $vente;
                if(isset($colonneTotalMention) && !isset($colonneTotalMention['mouts_'.$cvi])) { $colonneTotalMention['mouts_'.$cvi] = 0; }
                if(isset($colonneTotalMention)) { $colonneTotalMention['mouts_'.$cvi] += $c['mouts_'.$cvi]; }
            }
            array_push($colonnes, $c);
            $colonnes = array_merge($colonnes, $afterTotal);
        }

        if(isset($colonneTotalMention)) {
            array_push($colonnes, $colonneTotalMention);
        }

        $pages = array();

        $nb_colonnes_by_page = 6;
        $lasti = 0;
        for ($i = 0 ; $i < count($colonnes); ) {
            $page = array_slice($colonnes, $i, $nb_colonnes_by_page);
            $i += count($page) - 1;
            array_push($pages, $page);
            $lasti = ++$i;
        }

        $identification_enabled = 1;
        foreach($pages as $p) {
            $this->nb_pages++;
            $this->document->addPage($this->getPartial('dr_export/pageDR', array('dr' => $this->dr, 'libelle_appellation' => $libelle, 'colonnes_cepage' => $p, 'acheteurs' => $acheteurs, 'acheteursLignes' => $acheteursLignes, 'enable_identification' => $identification_enabled, 'nb_pages' => $this->nb_pages, 'hasLigneLieu' => $hasLigneLieu, 'hasLigneAppellation' => $hasLigneAppellation, 'hasVTSGN' => $hasVTSGN, 'has_no_usages_industriels' => $this->dr->recolte->getConfig()->hasNoUsagesIndustriels(), 'hasVci' => $this->dr->recolte->canHaveVci())));
            $identification_enabled = 0;
        }
  	}

    protected function getPartial($templateName, $vars = null) {
      return call_user_func_array($this->partial_function, array($templateName, $vars));
    }

}
