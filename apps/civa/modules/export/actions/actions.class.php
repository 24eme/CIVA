<?php

/**
 * printable actions.
 *
 * @package    civa
 * @subpackage printable
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */

class exportActions extends sfActions
{

  private static $type2douane = array('negoces' => 'L6', 'mouts' => 'L7', 'cooperatives' => 'L8');
  private function setAcheteurType($acheteurs, $type, $detail) {
    if ($detail->exist($type)) {
      foreach ($detail->{$type} as $n) {
	if (!isset($acheteurs[$detail->getCodeDouane()][$n->cvi][self::$type2douane[$type]])) {
	  $acheteurs[$detail->getCodeDouane()][$n->cvi][self::$type2douane[$type]]['cvi'] = $n->cvi;
	  $acheteurs[$detail->getCodeDouane()][$n->cvi][self::$type2douane[$type]]['volume'] = 0;
	}
	$acheteurs[$detail->getCodeDouane()][$n->cvi][self::$type2douane[$type]]['volume'] += $n->quantite_vendue;
      }
    }
    return $acheteurs;
  }

  public function executeXml(sfWebRequest $request) 
  {
    $recoltant = $this->getUser()->getRecoltant();
    $annee = $this->getRequestParameter('annee', null);
    $key = 'DR-'.$recoltant->cvi.'-'.$annee;
    $dr = sfCouchdbManager::getClient()->retrieveDocumentById($key);
    $xml = array();
    foreach ($dr->recolte->filter('^appellation_') as $appellation) {
      foreach ($appellation->filter('^lieu') as $lieu)  {
	foreach ($lieu->filter('^cepage_') as $cepage) {
	  //Comme il y a plusieurs acheteurs par lignes, il faut passer par une structure intermédiaire
	  $acheteurs = array();
	  //Liste des codes utilisés pour ce cepage (pour les balises avec un calcul cepage)
	  $cepage_code = array();
	  foreach ($cepage->detail as $detail) {
	    $code = $detail->getCodeDouane();
	    //Initialiation
	    if (!isset($xml[$code])) {
	      $col = array();
	      $col['L1'] = $code;
	      $col['L3'] = 'B';
	      $col['mentionVal'] = '';
	      $col['L4'] = 0;
	      if (isset($detail->motif_non_recolte) && $detail->motif_non_recolte)
		$col['motifSurfZero'] = $detail->motif_non_recolte;
	      $col['exploitant'] = array();
	      $col['exploitant']['L5'] = 0; //Volume total avec lies
	      $col['exploitant']['L9'] = 0; //Volume revendique sur place
	      $col['exploitant']['L10'] = 0; //Volume revendique non negoces
	      $col['exploitant']['L11'] = 0; //HS
	      $col['exploitant']['L12'] = 0; //HS
	      $col['exploitant']['L13'] = 0; //HS
	      $col['exploitant']['L14'] = 0; //Vin de table + Rebeches
	      $col['exploitant']['L15'] = 0; //Volume revendique
	      $col['exploitant']['L16'] = 0; //DPLC
	      $col['exploitant']['L17'] = 0; //HS
	      $col['exploitant']['L18'] = 0; //HS
	      $col['exploitant']['L19'] = 0; //HS
	      $xml[$code] = $col;
	      $acheteurs[$code] = array();
	      $cepage_code[$code] = 1;
	    }
	    //Renseigne les colonnes
	    $xml[$code]['L4'] += $detail->superficie;
	    $xml[$code]['exploitant']['L5'] += $detail->volume;
	    $xml[$code]['exploitant']['L9'] += $detail->cave_particuliere;
	    if ($detail->exist('cooperatives'))
	      foreach ($detail->cooperatives as $coop)  {
		$xml[$code]['exploitant']['L10'] += $coop->quantite_vendue;
	      }
	    $xml[$code]['exploitant']['L10'] += $detail->cave_particuliere;

	    $acheteurs = $this->setAcheteurType($acheteurs, 'negoces', $detail);
	    $acheteurs = $this->setAcheteurType($acheteurs, 'mouts', $detail);
	    $acheteurs = $this->setAcheteurType($acheteurs, 'cooperatives', $detail);

	    if (($detail->cepage == 'RB' && $detail->appellation == 'CREMANT') || $detail->appellation == 'VINTABLE') {
	      $xml[$code]['exploitant']['L14'] += $detail->volume;
	    }

	    $xml[$code]['exploitant']['L15'] += $detail->volume_revendique;
	    $xml[$code]['exploitant']['L16'] += $detail->volume_dplc;
	  }
	  //Pour chque code traité au niveau du cepage
	  foreach (array_keys($cepage_code) as $code) {
	    //Ajout des lies (au prorata)
	    $xml[$code]['exploitant']['L5'] += $xml[$code]['exploitant']['L5'] * $dr->getRatioLies();
	    $xml[$code]['exploitant']['L10'] += $xml[$code]['exploitant']['L10'] * $dr->getRatioLies();
	    //Ajout des acheteurs
	    if (isset($acheteurs[$code]))
	      foreach ($acheteurs[$code] as $cvi => $v) {
		$xml[$code]['exploitant'][] = $v;
	    }
	  }
	}
      }
    }
    $this->xml = $xml;
    $this->dr = $dr;
    $this->setLayout(false);
    $this->response->setContentType('text/xml');
  }
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executePdf(sfWebRequest $request)
  {
    $recoltant = $this->getUser()->getRecoltant();
    $annee = $this->getRequestParameter('annee', null);
    $key = 'DR-'.$recoltant->cvi.'-'.$annee;
    $dr = sfCouchdbManager::getClient()->retrieveDocumentById($key);
    try {
      $dr->recolte->filter('^appellation')->getFirst()->filter('^lieu')->getFirst()->filter('^cepage')->getFirst()->acheteurs;
    }catch(Exception $e) {
      $dr->update();
      $dr->save();
    }
    $this->forward404Unless($dr);

    $this->setLayout(false);
    //    $this->getResponse()->setContent('application/x-pdf');

    if ($this->getRequestParameter('output', 'pdf') == 'html') {
      $this->document = new PageableHTML('Déclaration de récolte '.$annee, $recoltant->nom, $annee.'_DR_'.$recoltant->cvi.'.pdf');
    }else {
      $this->document = new PageablePDF('Déclaration de récolte '.$annee, $recoltant->nom, $annee.'_DR_'.$recoltant->cvi.'.pdf');
    }

    foreach ($dr->getRecolte()->filter('^appellation_') as $appellation) {
      foreach ($appellation->filter('^lieu') as $lieu) {
	$this->createAppellationLieu($lieu, $recoltant);
      }
    }

    return $this->document->output();
    
  }

  private function createAppellationLieu($lieu, $recoltant) {
    $colonnes = array();
    $acheteurs = $lieu->acheteurs;
    $cpt = 0;
    foreach ($lieu->filter('^cepage_') as $cepage) {
      $i = 0;
      foreach ($cepage->detail as $detail) {
	$c = array();
	$c['type'] = 'detail';
	$c['cepage'] = $cepage->getLibelle();
	$c['denomination'] = $detail->denomination;
	$c['vtsgn'] = $detail->vtsgn;
	$c['superficie'] = $detail->superficie;
	$c['volume'] = $detail->volume;
	$c['cave_particuliere'] = $detail->cave_particuliere;
	$c['revendique'] = $detail->volume_revendique;
	$c['dplc'] = $detail->volume_dplc;
	foreach($detail->negoces as $vente) {
	  $c[$vente->cvi] = $vente->quantite_vendue;
	}
	foreach($detail->cooperatives as $vente) {
	  $c[$vente->cvi] = $vente->quantite_vendue;
	}
	$last = array_push($colonnes, $c) - 1;
	$i++;
	$cpt ++;
	/*
	if ($cpt > 8)
	  break 2;
	*/
      }
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
	$negoces = $cepage->getTotalAcheteursByCvi('negoces');
	foreach($negoces as $cvi => $total) {
	  $c[$cvi] = $total;
	}
	$coop =  $cepage->getTotalAcheteursByCvi('cooperatives');
	foreach($detail->cooperatives as $vente) {
	  $c[$vente->cvi] = $coop[$vente->cvi];
	}
	array_push($colonnes, $c);
	$cpt ++;
      }else{
	$colonnes[$last]['type'] = 'total';
      }
    }
    $c = array();
    $c['type'] = 'total';
    $c['cepage'] = 'Appellation';
    $c['denomination'] = 'Total';
    $c['vtsgn'] = '';
    $c['superficie'] = $lieu->total_superficie;
    $c['volume'] = $lieu->total_volume;
    $c['cave_particuliere'] = $lieu->getTotalCaveParticuliere();
    $c['revendique'] = $lieu->total_volume_revendique;
    $c['dplc'] = $lieu->total_dplc;
    $negoces = $lieu->getTotalAcheteursByCvi('negoces');
    foreach($negoces as $cvi => $vente) {
      $c[$cvi] = $vente;
    }
    $coop =  $lieu->getTotalAcheteursByCvi('cooperatives');
    foreach($detail->cooperatives as $vente) {
      $c[$vente->cvi] = $coop[$vente->cvi];
    }
    array_push($colonnes, $c);
    
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

    //L'identification des acheteurs ne peut apparaitre qu'une fois par cépage
    $identification_enabled = 1;
    foreach($pages as $p) {
      $this->document->addPage($this->getPartial('pageDR', array('recoltant'=>$recoltant, 'libelle_appellation' => $lieu->getLibelleWithAppellation(), 'colonnes_cepage' => $p, 'acheteurs' => $acheteurs, 'enable_identification' => $identification_enabled)));
      $identification_enabled = 0;
    }
  }
}
