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
          foreach ($dr->recolte->getConfigAppellations() as $appellation_config) {
            if ($dr->recolte->exist($appellation_config->getKey())) {
                $appellation = $dr->recolte->get($appellation_config->getKey());
                foreach ($appellation->getConfig()->filter('^lieu') as $lieu) {
                  if (!$appellation->exist($lieu->getKey()))
                    continue;
                  $lieu = $appellation->{$lieu->getKey()};
                  $this->hasLieuEditable = $appellation->getConfig()->hasLieuEditable();
                  if ($lieu->getConfig()->hasManyCouleur()) {
                  	$this->createAppellationCouleur($lieu, $tiers, $this->hasLieuEditable);
                  } else {
                  	$this->createAppellationLieu($lieu, $tiers, $this->hasLieuEditable);
                  }
                }
            }
          }
          if ($this->nb_pages == 0) {
              $extra = array('lies' => $dr->lies, 'jeunes_vignes' => $dr->jeunes_vignes);
              $this->document->addPage($this->getPartial('export/pageNoAppellationDR', array('tiers'=> $tiers, 'extra' => $extra)));
          }
        }
    }
    
    private function createAppellationCouleur($lieu, $tiers, $hasLieuEditable) {
	    foreach ($lieu->getConfig()->getCouleurs() as $couleur) {
		    $colonnes = array();
		    $afterTotal = array();
		    $acheteurs = $lieu->acheteurs;
		    $cpt = 0;
	    	$couleur = $lieu->{$couleur->getKey()};
		    foreach ($couleur->getConfig()->getCepages() as $cepage) {
		      if (!$couleur->exist($cepage->getKey()))
			continue;
		      $cepage = $couleur->{$cepage->getKey()};
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
		      }
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
		    }
	    $c = array();
	    $c['type'] = 'total';
	    $c['cepage'] = 'Total';
	    $c['denomination'] = ($lieu->getKey() == 'lieu') ? 'Appellation' : 'Lieu-dit';
	    if ($lieu->getAppellation()->getAppellation() == 'VINTABLE')
	      $c['denomination'] = '';
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
	
	    $extra = array('lies' => $couleur->getCouchdbDocument()->lies, 'jeunes_vignes' => $couleur->getCouchdbDocument()->jeunes_vignes);
	
	    $identification_enabled = 1;
	    foreach($pages as $p) {
	      $this->nb_pages++;
	      $this->document->addPage($this->getPartial('export/pageDR', array('tiers'=>$tiers, 'libelle_appellation' => $couleur->getLibelleWithAppellation(), 'colonnes_cepage' => $p, 'acheteurs' => $acheteurs, 'enable_identification' => $identification_enabled, 'extra' => $extra, 'nb_pages' => $this->nb_pages, 'hasLieuEditable' => $hasLieuEditable)));
	      $identification_enabled = 0;
	    }
	}
  }

    private function createAppellationLieu($lieu, $tiers, $hasLieuEditable) {
    $colonnes = array();
    $afterTotal = array();
    $acheteurs = $lieu->acheteurs;
    $cpt = 0;
    $couleur = $lieu->getCouleur();
    foreach ($couleur->getConfig()->getCepages() as $cepage) {
      if (!$couleur->exist($cepage->getKey()))
	continue;
      $cepage = $couleur->{$cepage->getKey()};
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
	//	$c['revendique'] = $detail->volume_revendique;
	//	$c['dplc'] = $detail->volume_dplc;
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
	/*
	if ($cpt > 8)
	  break 2;
	*/
      }
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
    }
    $c = array();
    $c['type'] = 'total';
    $c['cepage'] = 'Total';
    $c['denomination'] = ($lieu->getKey() == 'lieu') ? 'Appellation' : 'Lieu-dit';
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

    //add afterTOtal columns
    $colonnes = array_merge($colonnes, $afterTotal);

    $pages = array();

    //On peut pas mettre plus de 6 colonnes par page, si plus de 6 colonnes cepage
    //alors on coupe au total précédent
    $nb_colonnes_by_page = 6;
    $lasti = 0;
    for ($i = 0 ; $i < count($colonnes); ) {
      $page = array_slice($colonnes, $i, $nb_colonnes_by_page);
      $i += count($page) - 1;
      /*
      if (count($page) == $nb_colonnes_by_page) {
	while($page[$i - $lasti]['type'] != 'total') {
	  unset($page[$i - $lasti]);
	  $i--;
	}
      }
      */
      array_push($pages, $page);
      $lasti = ++$i;
    }

    $extra = array('lies' => $lieu->getCouchdbDocument()->lies, 'jeunes_vignes' => $lieu->getCouchdbDocument()->jeunes_vignes);

    //L'identification des acheteurs ne peut apparaitre qu'une fois par cépage
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