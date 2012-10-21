<?php

class ExportDRPdf {
    protected $type;
    protected $document;
    protected $nb_pages;
    protected $partial_name;
    protected $file_dir;
    protected $no_cache;

    public function __construct($dr, $tiers, $partial_function, $type = 'pdf', $file_dir = null, $no_cache = false, $filename = null) {
        $this->type = $type;
        $this->partial_function = $partial_function;
        $this->file_dir = $file_dir;
        $this->no_cache = $no_cache;

        $this->init($dr, $tiers, $filename);
        $this->create($dr, $tiers);
    }

    public function isCached() {
        return (!$this->no_cache && $this->document->isCached());
    }

    public function removeCache() {
        return $this->document->removeCache();
    }

    public function generatePDF() {
        return $this->document->generatePDF($this->no_cache);
    }

    public function addHeaders($response) {
        $this->document->addHeaders($response);
    }

    public function output() {
        return $this->document->output();
    }

    protected function init($dr, $tiers, $filename = null) {
        $validee = 'Non Validée';
        if ($dr->exist('validee')) {
          $validee = 'Déclaration validée le '.$dr->getDateValideeFr();
          if ($dr->exist('modifiee') && $dr->modifiee != $dr->validee) {
            $validee .= ' et modifiée le '.$dr->getDateModifieeFr();
          }
        }
        
        $title = 'Déclaration de récolte '.$dr->campagne;
        $header = $tiers->intitule.' '.$tiers->nom."\nCommune de déclaration : ".$dr->declaration_commune."\n".$validee;
        if (!$filename) {
            $filename = $dr->campagne.'_DR_'.$tiers->cvi.'_'.$dr->_rev.'.pdf';
        }

        if ($this->type == 'html') {
          $this->document = new PageableHTML($title, $header, $filename, $this->file_dir);
        }else {
          $this->document = new PageablePDF($title, $header, $filename, $this->file_dir);
        }
    }

    protected function create($dr, $tiers) {
        $this->nb_pages = 0;
        if (!$this->isCached()) {
          foreach ($dr->recolte->certification->genre->getConfigAppellations() as $appellation_config) {
            if ($dr->recolte->certification->genre->exist($appellation_config->getKey())) {
                $appellation = $dr->recolte->certification->genre->get($appellation_config->getKey());
                foreach ($appellation->getConfig()->getLieux() as $lieu) {
                  if (!$appellation->getLieux()->exist($lieu->getKey()))
                    continue;
                  $lieu = $appellation->getLieux()->get($lieu->getKey());
                  $this->createAppellationLieu($lieu, $tiers, $appellation->getConfig()->hasLieuEditable());
                }
            }
          }

      $infos = $this->getRecapitulatifInfos($dr);
      $infosPage = array();
	  $nb_colonnes_by_page = 6;
	  $infos = $this->getRecapitulatifInfos($dr);
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
                    'volume_sur_place' => array_slice($infos['volume_sur_place'], $i, $nb_colonnes_by_page),
                    'revendique' => array_slice($infos['revendique'], $i, $nb_colonnes_by_page),
      				'usages_industriels' => array_slice($infos['usages_industriels'], $i, $nb_colonnes_by_page),
      				'total_superficie' => $infos['total_superficie'],
        			'total_volume' => $infos['total_volume'],
                    'total_usages_industriels' => $infos['total_usages_industriels'],
        			'total_revendique' => $infos['total_revendique'],
        			'lies' => $infos['lies'],
        			'jeunes_vignes' => $infos['jeunes_vignes']
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
    			$this->document->addPage($this->getPartial('export/recapitulatif', array('tiers'=> $tiers, 'infos'=> $infosPage[$key], 'has_total' => $has_total)));
    			$currentPage++;
    		}
          	
          } else {
          	$this->document->addPage($this->getPartial('export/recapitulatif', array('tiers'=> $tiers, 'infos'=> $infos, 'has_total' => true)));
          }
        }
    }
    
    private function getRecapitulatifInfos($dr)
    {
        $appellations = array();
        $superficie = array();
        $volume = array();
        $volume_sur_place = array();
        $revendique = array();
        $usages_industriels = array();
        $libelle = array();
        $volume_negoces = array();
        $volume_cooperatives = array();
        $cvi = array();
        foreach ($dr->recolte->certification->genre->getConfig()->getAppellations() as $appellation_key => $appellation_config) {
          if ($dr->recolte->certification->genre->exist($appellation_key)) {
              $appellation = $dr->recolte->certification->genre->get($appellation_key);
              if ($appellation->getConfig()->excludeTotal())
                continue;
              $appellations[] = $appellation->getAppellation();
              $libelle[$appellation->getAppellation()] = $appellation->getConfig()->getLibelle();
              $superficie[$appellation->getAppellation()] = $appellation->getTotalSuperficie();
              $volume[$appellation->getAppellation()] = $appellation->getTotalVolume();
              $revendique[$appellation->getAppellation()] = $appellation->getVolumeRevendique();
              $usages_industriels[$appellation->getAppellation()] = $appellation->getTotalUsagesIndustriels();
              $volume_sur_place[$appellation->getAppellation()] = $appellation->getTotalCaveParticuliere();
          }
        }
        $infos = array();
        $infos['appellations'] = $appellations;
        $infos['libelle'] = $libelle;
        $infos['superficie'] = $superficie;
        $infos['volume'] = $volume;
        $infos['volume_sur_place'] = $volume_sur_place;
        $infos['revendique'] = $revendique;
        $infos['usages_industriels'] = $usages_industriels;
        $infos['total_superficie'] = array_sum(array_values($superficie));
        $infos['total_volume'] = array_sum(array_values($volume));
        $infos['total_volume_sur_place'] = array_sum(array_values($volume_sur_place));
        $infos['total_usages_industriels'] = array_sum(array_values($usages_industriels));
        $infos['total_revendique'] = array_sum(array_values($revendique));
        $infos['lies'] = $dr->lies;
        $infos['jeunes_vignes'] = $dr->jeunes_vignes;
        return $infos;
    }
    
	private function createAppellationLieu($lieu, $tiers, $hasLieuEditable) {
      $hasManyCouleur = $lieu->getConfig()->getNbCouleurs() > 1;
    	$colonnes = array();
    	$afterTotal = array();
    	$acheteurs = $lieu->acheteurs;
   		$cpt = 0;
    	foreach ($lieu->getCouleurs() as $couleur) {
        $nbCepageCouleur = 0;
	    	foreach ($couleur->getConfig()->getCepages() as $cepage_config) {
          if (!$couleur->exist($cepage_config->getKey())) {
            continue;
          }
          $cepage = $couleur->get($cepage_config->getKey());

			    if (!count($cepage->detail))
			     continue;
    		  $i = 0;
    		  foreach ($cepage->detail as $detail) {
  					$c = array();
  					$c['type'] = 'detail';
  					$c['cepage'] = $cepage->getLibelle();
  					$c['denomination'] = $detail->denomination;
  					$c['vtsgn'] = $detail->vtsgn;
  					$c['superficie'] = $detail->superficie;
  					$c['volume'] = $detail->volume;
  					if ($hasLieuEditable)
  						$c['lieu'] = $detail->lieu;
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
  					if ($detail->exist('mouts'))
  		  				foreach($detail->mouts as $vente) {
  		    				$c['mouts_'.$vente->cvi] = $vente->quantite_vendue;
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
  		  				$c['cepage'] = $cepage->getLibelle();
  		  				$c['denomination'] = 'Total';
  		  				$c['vtsgn'] = '';
  		  				$c['superficie'] = $cepage->total_superficie;
  		  				$c['volume'] = $cepage->total_volume;
  		  				$c['cave_particuliere'] = $cepage->getTotalCaveParticuliere();
  		  				$c['revendique'] = $cepage->volume_revendique;
  		  				$c['dplc'] = $cepage->dplc;
  		  				if (!$c['dplc'])
  		    				$c['dplc'] = '0,00';
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
  		  					$colonnes[$last]['dplc'] = $cepage->dplc;
  		  					if (!$colonnes[$last]['dplc'])
  		    					$colonnes[$last]['dplc'] = '0,00';
  						}
	      		}
	      		$nbCepageCouleur++;
	    	} // endforeach; cepages
	    	if ($hasManyCouleur && $nbCepageCouleur > 0) {
		    	$c = array();
			    $c['type'] = 'total';
			    $c['cepage'] = 'Total';
				if ($hasLieuEditable)
					$c['lieu'] = $couleur->libelle;
				else
			    	$c['denomination'] = $couleur->libelle;
			    $c['vtsgn'] = '';
			    $c['superficie'] = $couleur->total_superficie;
			    $c['volume'] = $couleur->total_volume;
			    $c['cave_particuliere'] = $couleur->getTotalCaveParticuliere();
			    $c['revendique'] = $couleur->volume_revendique;
			    $c['dplc'] = $couleur->dplc;
			    if (!$c['dplc'])
			      $c['dplc'] = '0,00';
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
    	$c['cepage'] = 'Total';
    	$c['denomination'] = ($lieu->getKey() == 'lieu') ? 'Appellation' : '';
    	if ($lieu->getAppellation()->getAppellation() == 'VINTABLE')
      		$c['denomination'] = '';
    	$c['vtsgn'] = '';
	    $c['superficie'] = $lieu->total_superficie;
	    $c['volume'] = $lieu->total_volume;
	    $c['cave_particuliere'] = $lieu->getTotalCaveParticuliere();
	    $c['revendique'] = $lieu->volume_revendique;
	    $c['dplc'] = $lieu->dplc;
	    if (!$c['dplc'])
	      $c['dplc'] = '0,00';
	    $negoces = $lieu->getVolumeAcheteurs('negoces');
	    foreach($negoces as $cvi => $vente) {
	      $c['negoces_'.$cvi] = $vente;
	    }
	    $coop =  $lieu->getVolumeAcheteurs('cooperatives');
	    foreach($coop as $cvi => $vente) {
	      $c['cooperatives_'.$cvi] = $vente;
	    }
	    $mouts =  $lieu->getVolumeAcheteurs('mouts');
	    foreach($mouts as $cvi => $vente) {
	      $c['mouts_'.$cvi] = $vente;
	    }
	    array_push($colonnes, $c);
    	$colonnes = array_merge($colonnes, $afterTotal);
    	$pages = array();

    	$nb_colonnes_by_page = 6;
    	$lasti = 0;
    	for ($i = 0 ; $i < count($colonnes); ) {
      		$page = array_slice($colonnes, $i, $nb_colonnes_by_page);
      		$i += count($page) - 1;
      		array_push($pages, $page);
      		$lasti = ++$i;
    	}
    	$extra = array('lies' => $lieu->getCouchdbDocument()->lies, 'jeunes_vignes' => $lieu->getCouchdbDocument()->jeunes_vignes);
    	$identification_enabled = 1;
	    foreach($pages as $p) {
	      $this->nb_pages++;
	      $this->document->addPage($this->getPartial('export/pageDR', array('tiers'=>$tiers, 'libelle_appellation' => $lieu->getLibelleWithAppellation(), 'colonnes_cepage' => $p, 'acheteurs' => $acheteurs, 'enable_identification' => $identification_enabled, 'extra' => $extra, 'nb_pages' => $this->nb_pages, 'hasLieuEditable' => $hasLieuEditable)));
	      $identification_enabled = 0;
	    }
  	}

  protected function getPartial($templateName, $vars = null) {
      return call_user_func_array($this->partial_function, array($templateName, $vars));
  }

}
