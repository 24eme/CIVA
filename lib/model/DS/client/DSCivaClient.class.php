<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class DSCivaClient
 * @author mathurin
 */
class DSCivaClient extends DSClient {

    const STATUT_VALIDE = 'VALIDE';
    const STATUT_A_SAISIR = 'A_SAISIR';
    
    const VOLUME_NORMAL = 'normal';
    const VOLUME_VT = 'VT';
    const VOLUME_SGN = 'SGN';

    const ETAPE_EXPLOITATION = 1;
    const ETAPE_LIEUX_STOCKAGE = 2;
    const ETAPE_STOCKS = 3;
    const ETAPE_AUTRES = 4;
    const ETAPE_VALIDATION = 5;
    const ETAPE_VISUALISATION = 6;

    public static $etapes = array(
                                self::ETAPE_EXPLOITATION => "Exploitation", 
                                self::ETAPE_LIEUX_STOCKAGE => "Lieux de stockage", 
                                self::ETAPE_STOCKS => "Stocks", 
                                self::ETAPE_AUTRES => "Autres Produits", 
                                self::ETAPE_VALIDATION => "Validation", 
                                 );

    public function buildId($identifiant, $periode, $lieu_stockage) {
        return sprintf('DS-%s-%s-%s', $identifiant, $periode, $lieu_stockage);
    }
    

    public function buildCampagne($periode) {

        return ConfigurationClient::getInstance()->buildCampagne($this->buildDate($periode));
    }

    public function getAnnee($periode) {

        return preg_replace('/([0-9]{4})([0-9]{2})/', '$1', $periode);
    }

    public function getMois($periode) {

        return preg_replace('/([0-9]{4})([0-9]{2})/', '$2', $periode);
    }
    
    public function createDateStock($date_stock) {
	if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $date_stock)) {
		return $date_stock;
        }
        $v = date_create_from_format('d/m/Y',$date_stock);
	if (!$v) {
		throw new sfException("Unexepected date format for $date_stock");
	}
        return $v->format('Y-m-d');
    }
    
    public function retrieveDsPrincipalesByCampagneAndCvi($cvi,$campagne) {
        $dss_principales = array();
        $annee = $campagne + 1;
        $docs = $this->startkey('DS-'.$cvi.'-000000-000')->endkey('DS-'.$cvi.'-'.$annee.'99-999')->execute(acCouchdbClient::HYDRATE_ON_DEMAND);
        foreach($docs->getIds() as $doc_id) {
            if(preg_match('/DS-(?P<cvi>\d+)-(?P<campagne>\d+)/', $doc_id, $matches)){
                $campagne_t = preg_replace('/^([0-9]{4})([0-9]{2})$/', "$1", $matches['campagne']);
                if(!array_key_exists($campagne_t, $dss_principales))
                    $dss_principales[$campagne_t] = $this->getDSPrincipaleByDs($this->find($doc_id,  acCouchdbClient::HYDRATE_DOCUMENT));
            }
        }
        return $dss_principales;
    }


    public function findByCviAndCampagne($cvi, $campagne) {
        $tiers = acCouchdbManager::getClient('_Tiers')->findByCvi($cvi);
        for($month=8;$month>0;$month--){
            if($ds = $this->find('DS-'.$cvi.'-'.($campagne+1).sprintf("%02d",$month).'-'.$tiers->getLieuStockagePrincipal()->getNumeroIncremental())){
                return $ds;
            }
        }
        for($month=8;$month<13;$month++){
            if($ds = $this->find('DS-'.$cvi.'-'.$campagne.sprintf("%02d",$month).'-'.$tiers->getLieuStockagePrincipal()->getNumeroIncremental())){
                return $ds;
            }
        }
        return null;
    }

    public function retrieveByCampagneAndCvi($cvi,$campagne) {
        
        return $this->findByCviAndCampagne($cvi, $campagne);
    }
    
   public function buildPeriode($date) {
        preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $date, $matches);
        
        return sprintf('%d%02d', $matches[1], '07');
    }
    
    
    public function findOrCreateDssByTiers($tiers, $date_stock, $ds_neant = false) {
        $periode = $this->buildPeriode($this->createDateStock($date_stock));
        $cpt = 1;
        $dss = array();
        $ds_principale_exist = false;
        foreach ($tiers->lieux_stockage as $lieux_stockage) {
            
            $num_lieu = $lieux_stockage->getNumeroIncremental();
            $ds = $this->findByIdentifiantAndPeriode($tiers->cvi, $periode, $num_lieu);
            if($ds) continue;
            
            $ds = new DSCiva();
            $ds->date_emission = date('Y-m-d');
            $ds->date_stock = $this->createDateStock($date_stock);
            $ds->identifiant = $tiers->cvi;
            $ds->_id = sprintf('DS-%s-%s-%s', $ds->identifiant, $periode, $num_lieu);
            if(!$ds_principale_exist){
                $ds->add('ds_principale',1);
                $ds_principale_exist = true;
            }else{
                $ds->add('ds_principale',0);
            }
            $ds->storeInfos();
            if(!$ds_neant){
                $ds->updateProduits();
            }else{
               $ds->add('ds_neant',1);
            }
            $ds->updateAutre();
            if(!$ds->isDsPrincipale() && $ds_neant){
                continue;
            }
            if($ds->isDsPrincipale())
                $ds->add('num_etape',1);
           
            $dss[] = $ds;
            $cpt++;
        }	
        return $dss;
    } 
    
    public function createDsByDsPrincipaleAndLieu($ds,$lieu_num) {
        $new_ds = new DSCiva();
        $new_ds->date_emission = $ds->date_emission;
        $new_ds->date_stock = $ds->date_stock;
        $new_ds->identifiant = $ds->identifiant;
        $new_ds->_id = sprintf('DS-%s-%s-%s', $new_ds->identifiant, $ds->getPeriode(), $lieu_num);
        $new_ds->add('ds_principale',0);
        $new_ds->storeInfos();
        return $new_ds;
    }

    public function findDssByCviAndPeriode($cvi, $periode, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        $base_id = sprintf('DS-%s-%s', $cvi, $periode);
        $ids = $this->startkey($base_id.'-001')->endkey($base_id.'-999')->execute(acCouchdbClient::HYDRATE_ON_DEMAND_WITH_DATA)->getIds();
        $dss = array();
        foreach($ids as $id) {
            $dss[$id] = $this->find($id, $hydrate);
        }
        return $dss;
    }

     public function removeAllDssByCvi($tiers, $date_stock) {
        $periode = $this->buildPeriode($this->createDateStock($date_stock));
        $dss = $this->findDssByCviAndPeriode($tiers->cvi, $periode);
	$dssIds = array();
	foreach($dss as $ds) {
	    $dssIds[] = $ds->_id;
            $this->delete($ds);
        }
	return $dssIds;
    }
    
    public function findDssByCvi($tiers, $date_stock, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        $periode = $this->buildPeriode($this->createDateStock($date_stock));

        return $this->findDssByCviAndPeriode($tiers->cvi, $periode, $hydrate);
    }
    
    public function getNextLieuStockageSecondaireByCviAndDate($cvi, $date_stock) {
        $periode = $this->buildPeriode($this->createDateStock($date_stock));
        $tiers = acCouchdbManager::getClient('_Tiers')->findByCvi($cvi);
        if(!$tiers) {

            throw new sfException(sprintf("Tiers not found %s", $cvi));
        }
        foreach($tiers->lieux_stockage as $lieu_stockage) {
            if($lieu_stockage->isPrincipale()) {
                continue;
            }
            $ds = $this->findByIdentifiantAndPeriode($cvi, $periode, $lieu_stockage->getNumeroIncremental());
            if(!$ds) {

                return $lieu_stockage->getNumeroIncremental(); 
            }
        }

        throw new sfException(sprintf("Plus aucun lieu de stockage n'existe pour le tiers %s après le numéro de lieu de stockage %s", $cvi, $lieu_stockage->getNumeroIncremental()));
    }


    public function getNextDS($ds) {
        if(!$ds) {
            throw new sfException("La DS passée en argument de getNextLieuStockage ne peut pas être null");
        }

        $dss = $this->findDssByDS($ds);

        foreach($dss as $d) {
            if($d->_id == $ds->_id) {
               break;
            }
        }

        return current($dss);
    }
    
    public function findDssByDS($ds, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        if(!$ds)
            throw new sfException("La DS passée en argument de findDssByDS ne peut pas être null");
        return $this->findDssByCviAndPeriode($ds->identifiant, $ds->periode, $hydrate);
    }

    public function findDSByLieuStockageAndCampagne($lieu_num, $campagne){
        return $this->find($this->buildId($identifiant, $periode, $lieu_stockage));
    }

    public function getDSPrincipale($tiers, $date_stock){
        $dss = $this->findDssByCvi($tiers, $date_stock);
        foreach ($dss as $ds) {
            if($ds->isDsPrincipale())
            return $ds;
        }

        return $ds;
    }
    
    public function getDSPrincipaleByDs($ds) {
        $dss = $this->findDssByDS($ds);        
        foreach ($dss as $current_ds) {
            $current_ds->_id;
            if($current_ds->isDsPrincipale()){
                return $current_ds;
            }
        }
        return null;
    }

    
     public function getFirstDSByDs($ds) {
        $dss = $this->findDssByDS($ds);     
        foreach ($dss as $current_ds) {
                return $current_ds;
        }
        return null;
    }
    
    public function createOrFind($etablissementId, $date_stock) {
      throw sfException('createOrFind deprecated use findOrCreateDsByEtbId instead');
    }

    public function getHistoryByOperateur($etablissement) {
        return 1;
//        return DSHistoryView::getInstance()->findByEtablissementDateSorted($etablissement->identifiant);
    }
    
    
    public function create($data, $force_return_ls = false) {
        if (!isset($data->type)) {
            
            throw new acCouchdbException('Property "type" ($data->type)');
        }
        if (!class_exists($data->type)) {
            
            throw new acCouchdbException('Class "' . $data->type . '" not found');
        }

        if($data->type == "LS" && $force_return_ls == false )
          return $this->find($data->pointeur);
        
        $doc = new DSCiva();
        $doc->loadFromCouchdb($data);


        
        return $doc;
    }

    
    public function getLibelleFromId($id) {
       if (!preg_match('/^DS-[0-9]+-([0-9]{4})([0-9]{2})/', $id, $matches)) {
            
            return $id;
        }
        sfContext::getInstance()->getConfiguration()->loadHelpers(array('Orthographe','Date'));
        $origineLibelle = 'DS de';
        $annee = $matches[1];
        $mois = $matches[2];
        $date = $annee.'-'.$mois.'-01';
        $df = format_date($date,'MMMM yyyy','fr_FR');
        return elision($origineLibelle,$df);
    }
    
    public function getTotauxByAppellationsRecap($ds) {
        $dss = $this->findDssByDS($ds);
        $totauxByAppellationsRecap = array();

        $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap, 'ALSACEBLANC', null, "AOC Alsace Blanc");
        $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap, 'ALSACEROUGEROSE', null, "Rouge ou Rosé");
        $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap, 'GRDCRU', null, "AOC Alsace Grands Crus");
        $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap, 'CREMANT', null, "AOC Crémant d'Alsace");

        foreach ($dss as $ds_key => $ds) {
            foreach ($ds->declaration->getAppellationsSorted() as $app_key => $appellation){                
                switch ($appellation_key = preg_replace('/^appellation_/', '', $app_key)) {
                    case 'GRDCRU':
                    case 'CREMANT': 
                        $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap,$appellation_key,$appellation,$appellation->getLibelle());
                        break;
                    case 'ALSACEBLANC':
                    case 'COMMUNALE':
                    case 'LIEUDIT':
                    case 'PINOTNOIRROUGE':
                    case 'PINOTNOIR':
                        $totauxByAppellationsRecap = $this->getTotauxAgregeByCouleur($totauxByAppellationsRecap,$appellation_key,$appellation);
                        break;
                }
                
            }
        }
    return $totauxByAppellationsRecap;
    }
    
    public function getTotauxAgregeByCouleur($totauxByAppellationsRecap,$app_key,$appellation) {
        if(preg_match('/^PINOTNOIR/', $app_key)){
            return $this->getTotauxWithNode($totauxByAppellationsRecap,'ALSACEROUGEROSE',$appellation,'Rouge ou Rosé');
        }
        foreach ($appellation->getLieux() as $lieu) {
            foreach ($lieu->getCouleurs() as $couleur_key => $couleur) {
                if(preg_match('/Rouge$/', $couleur_key)){
                    $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap,'ALSACEROUGEROSE',$couleur,'Rouge ou Rosé');
                }
                else
                {
                    $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap,'ALSACEBLANC',$couleur,'AOC Alsace Blanc');
                }
            }
        }
        return $totauxByAppellationsRecap;
    }
    
    public function getTotauxWithNode($totauxByAppellationsRecap,$key,$node,$nom) {
        if(!array_key_exists($key, $totauxByAppellationsRecap)){
            $totauxByAppellationsRecap[$key] = new stdClass();
            $totauxByAppellationsRecap[$key]->nom = 'TOTAL '.$nom;
            $totauxByAppellationsRecap[$key]->volume_total = null;
            $totauxByAppellationsRecap[$key]->volume_normal = null;
            $totauxByAppellationsRecap[$key]->volume_vt = null;
            $totauxByAppellationsRecap[$key]->volume_sgn = null;
        }

        if(!$node) {

            return $totauxByAppellationsRecap;
        }

        $totauxByAppellationsRecap[$key]->volume_total += ($node->total_stock)? $node->total_stock : 0;
        $totauxByAppellationsRecap[$key]->volume_normal += ($node->total_normal)? $node->total_normal : 0;
        $totauxByAppellationsRecap[$key]->volume_vt += ($node->total_vt)? $node->total_vt : 0;
        $totauxByAppellationsRecap[$key]->volume_sgn += ($node->total_sgn)? $node->total_sgn : 0;
        return $totauxByAppellationsRecap;
    }  
    
    public function getTotalAOC($ds) {
        $dss = $this->findDssByDS($ds);
        $totalAOC = 0;
        foreach ($dss as $ds_key => $ds) {
            $totalAOC += $ds->getTotalAOC();
        }
        return $totalAOC;
    }
    
    public function getTotalSansIG($ds) {
        $dss = $this->findDssByDS($ds);
        $totalSansIG = 0;
        foreach ($dss as $ds_key => $ds) {
            $totalSansIG += $ds->getTotalVinSansIg();
        }
        return $totalSansIG;
    }
    
    public function getTotalSansIGMousseux($ds) {
        $dss = $this->findDssByDS($ds);
        $totalMousseuxSansIG = 0;
        foreach ($dss as $ds_key => $ds) {
            $totalMousseuxSansIG += $ds->getTotalMousseuxSansIg();
        }
        return $totalMousseuxSansIG;
    }
    
    public function getPreviousDS($ds) {
        $dss = $this->findDssByDS($ds);
        while($current_ds = array_pop($dss)){
            if(($current_ds->_id == $ds->_id) && count($dss)){
                return array_pop($dss);
            }
        }
        return null;
    }
    
    public function getNbDS($ds){
        return count($this->findDssByDS($ds));
    }

    public function storeInfos($ds) {
        if(!$ds->isDsPrincipale()){
            throw new sfException("Cette méthode n'est autorisé uniqument partir d'une ds principale");
        }
        
        $dss = $this->findDssByDs($ds);
        foreach ($dss as $current_ds) {
            $current_ds->storeInfos();
            $current_ds->save();
        }

        return $dss;
    }
    
    public function validate($ds,$compteValidateurId){
        if(!$ds->isDsPrincipale()){
            throw new sfException("Aucun clean n'est possible à partir d'une DS qui n'est pas la Ds Principale (ici : $ds->_id)");
        }
        
        $dss = $this->findDssByDs($ds);
        foreach ($dss as $current_ds) {
            $current_ds->declaration->cleanAllNodes();
            
            $deleted = false;
            
            if($current_ds->hasNoAppellation() && !$current_ds->isDsPrincipale()){
                $num_etape = $ds->getNumEtapeAbsolu();
                $this->delete($current_ds);
                $ds->updateEtape($num_etape);
                $ds->save();
                $deleted = true;
            }
            if(!$deleted){
                $current_ds->validate(date("Y-m-d"),$compteValidateurId);
                $current_ds->update();
                $current_ds->save();
            }
        }
        return $dss;
    }

    public function devalidate($ds, $juste_civa = false){
        if(!$ds->isDsPrincipale()){
            throw new sfException("La devalidation n'est possible qu'a partir d'une ds principale");
        }
        
        $dss = $this->findDssByDs($ds);
        foreach ($dss as $current_ds) {
            $current_ds->devalidate($juste_civa);
            $current_ds->save();
        }

        return $dss;
    }
    
    public function getAllIdsByCampagne($campagne){
        $ids = $this->startkey('DS-0000000000-000000-000')->endkey('DS-9999999999-99999-999')->execute(acCouchdbClient::HYDRATE_ON_DEMAND_WITH_DATA)->getIds();
        $result_ids = array();
        foreach ($ids as $id) {
            if(preg_match('/^DS-[0-9]{10}-'.$campagne.'[0-9]{2}-[0-9]{3}$/', $id)){
                $result_ids[] = $id;
            }
        }
        return $result_ids;
    }
    
    public function changeDSPrincipale($dss,$last_ds_principale,$num_new_principale) {
        if($last_ds_principale->getLieuStockage() == $num_new_principale){
            return $dss;
        }
    //    var_dump($last_ds_principale->_id); exit;
        $num_etape = $last_ds_principale->get('num_etape');
        $rebeches = $last_ds_principale->get('rebeches');
        $dplc = $last_ds_principale->get('dplc');
        $lies = $last_ds_principale->get('lies');
        $mouts = $last_ds_principale->get('mouts');
        $date_depot_mairie = ($last_ds_principale->exist('date_depot_mairie'))? $last_ds_principale->get('date_depot_mairie') : null;
        
        $new_dss = array();
        
        foreach ($dss as $key => $current_ds) {
            if($current_ds->getLieuStockage() == $num_new_principale){
                $current_ds->add('ds_principale',1);
                $current_ds->add('num_etape',$num_etape);                
                $current_ds->add('rebeches',$rebeches);
                $current_ds->add('dplc',$dplc);
                $current_ds->add('lies',$lies);
                $current_ds->add('mouts',$mouts);
                if($date_depot_mairie){
                    $current_ds->add('date_depot_mairie',$date_depot_mairie);                
                }
                $current_ds->add('courant_stock',$current_ds->getFirstAppellation()->getHash());
                $new_dss[$key] = $current_ds;
                
            }else{
                $current_ds->add('ds_principale',0);
                $current_ds->remove('num_etape'); 
                if($date_depot_mairie){      
                    $current_ds->remove('date_depot_mairie');    
                }   
                $current_ds->remove('courant_stock');       
                $current_ds->add('rebeches',null);
                $current_ds->add('dplc',null);
                $current_ds->add('lies',null);
                $current_ds->add('mouts',null);
                $new_dss[$key] = $current_ds;
            }
        }
        return $new_dss;
    }
}
