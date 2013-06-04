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
        $docs = $this->startkey('DS-'.$cvi.'-'.$campagne.'-000')->endkey('DS-'.$cvi.'-9999-999')->execute(acCouchdbClient::HYDRATE_ON_DEMAND);
        foreach($docs->getIds() as $doc_id) {
            if(preg_match('/DS-(?P<cvi>\d+)-(?P<campagne>\d+)/', $doc_id, $matches)){
                $campagne_t = preg_replace('/^([0-9]{4})([0-9]{2})$/', "$1", $matches['campagne']);
                if(!array_key_exists($campagne_t, $dss_principales))
                    $dss_principales[$campagne_t] = $this->getDSPrincipaleByDs($this->find($doc_id,  acCouchdbClient::HYDRATE_JSON));
            }
        }
        return $dss_principales;
    }


    public function retrieveByCampagneAndCvi($cvi,$campagne) {
        for($month=1;$month<13;$month++){
            if($ds = $this->find('DS-'.$cvi.'-'.$campagne.sprintf("%02d",$month).'-001')){
                return $ds;
            }
        }
        return null;
    }
    
    
    public function findOrCreateDssByTiers($tiers, $date_stock) {
        $periode = $this->buildPeriode($this->createDateStock($date_stock));
        $cpt = 1;
        $dss = array();
        
        foreach ($tiers->lieux_stockage as $lieux_stockage) {
            
            $num_lieu = sprintf("%03d",$cpt);
            $ds = $this->findByIdentifiantAndPeriode($tiers->cvi, $periode, $num_lieu);
            if($ds) continue;
            
            $ds = new DSCiva();
            $ds->date_emission = date('Y-m-d');
            $ds->date_stock = $this->createDateStock($date_stock);
            $ds->identifiant = $tiers->cvi;
            $ds->storeDeclarant();
            $ds->_id = sprintf('DS-%s-%s-%s', $ds->identifiant, $periode, $num_lieu);
            $ds->updateProduits();
            $ds->updateAutre();
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
        $new_ds->storeDeclarant();
        $new_ds->_id = sprintf('DS-%s-%s-%s', $new_ds->identifiant, $ds->getPeriode(), $lieu_num);
        return $new_ds;
    }


    public function findDssByCvi($tiers, $date_stock) {
        $periode = $this->buildPeriode($this->createDateStock($date_stock));
        $cpt = 1;
        $dss = array();
        foreach ($tiers->lieux_stockage as $lieux_stockage) {
            
            $num_lieu = sprintf("%03d",$cpt);
            $ds = $this->findByIdentifiantAndPeriode($tiers->cvi, $periode, $num_lieu);
            if(!$ds) throw new sfException(sprintf('La Ds du recoltant de cvi %s pour la periode %s et le lieu de stockage %s n\'existe pas',
                                           $tiers->cvi, $periode, $lieux_stockage->nom));
            $dss[] = $ds;
            $cpt++;
        }	
        return $dss;
    }
    
    public function getNextLieuStockageByCviAndDate($cvi, $date_stock) {
        $periode = $this->buildPeriode($this->createDateStock($date_stock));
        $cpt = 2;
        while($ds = $this->findByIdentifiantAndPeriode($cvi, $periode, sprintf("%03d",$cpt))){
            $cpt++;
        }
        return sprintf("%03d",$cpt);
    }


    public function getNextDS($ds) {
        if(!$ds)
            throw new sfException("La DS passée en argument de getNextLieuStockage ne peut pas être null");
        $matches = array();
        if(preg_match('/^DS-([0-9]{10})-([0-9]{6})-([0-9]{3})$/', $ds->_id,$matches)){
            if(!isset($matches[3]))
                throw new sfException("La DS $ds->_id possède un identifiant non conforme");
            
            $lieu_stockage = $matches[3];
            $next_lieu = $lieu_stockage+1;
            $next_id = 'DS-'.$matches[1].'-'.$matches[2].'-'.sprintf("%03d",$next_lieu);
            return $this->find($next_id);
        }
        throw new sfException("La DS $ds->_id possède un identifiant non conforme");
    }
    
    public function findDssByDS($ds) {
        if(!$ds)
            throw new sfException("La DS passée en argument de findDssByDS ne peut pas être null");
        $matches = array();
        
        if(preg_match('/^DS-([0-9]{10})-([0-9]{6})-([0-9]{3})$/', $ds->_id,$matches)){
            if(!isset($matches[3]))
                throw new sfException("La DS $ds->_id possède un identifiant non conforme");            
            $lieu_stockage = 1;
            $id = 'DS-'.$matches[1].'-'.$matches[2].'-'.sprintf("%03d",$lieu_stockage);
            
            $dss = array();
            while($current_ds = $this->find($id)){
              $dss[$id] = $current_ds;
              $id = 'DS-'.$matches[1].'-'.$matches[2].'-'.sprintf("%03d",$lieu_stockage++);
            }
            
            return $dss;
        }
        throw new sfException("La DS $ds->_id possède un identifiant non conforme");
    }

    public function findDSByLieuStockageAndCampagne($lieu_num, $campagne){
        return $this->find($this->buildId($identifiant, $periode, $lieu_stockage));
    }

    public function getDSPrincipale($tiers, $date_stock){
        $dss = $this->findDssByCvi($tiers, $date_stock);
        return $dss[0];
    }
    
    public function getDSPrincipaleByDs($ds) {
        foreach ($this->findDssByDS($ds) as $ds) {
            if($ds->isDsPrincipale()) return $ds;
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
        foreach ($dss as $ds_key => $ds) {
            foreach ($ds->declaration->getAppellationsSorted() as $app_key => $appellation){                
                switch ($appellation_key = preg_replace('/^appellation_/', '', $app_key)) {
                    case 'VINDETABLE':
                    break;
                
                    case 'GRDCRU':
                    case 'CREMANT': 
                    case 'ALSACEBLANC': 
                        $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap,$appellation_key,$appellation,$appellation->getLibelle());
                    break;
                    default :
                      //  $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap,'ALSACE',$appellation,'AOC Alsace');
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
                if(preg_match('/Blanc$/', $couleur_key)){
                    $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap,'ALSACEBLANC',$couleur,'AOC Alsace Blanc');
                }
                else
                {
                    $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap,'ALSACEROUGEROSE',$couleur,'Rouge ou Rosé');
                }
            }
        }
        return $totauxByAppellationsRecap;
    }
    
    public function getTotauxWithNode($totauxByAppellationsRecap,$key,$node,$nom) {
        if(!array_key_exists($key, $totauxByAppellationsRecap)){
            $totauxByAppellationsRecap[$key] = new stdClass();
            $totauxByAppellationsRecap[$key]->nom = 'TOTAL '.$nom;
            $totauxByAppellationsRecap[$key]->volume_total = 0;
            $totauxByAppellationsRecap[$key]->volume_normal = 0;
            $totauxByAppellationsRecap[$key]->volume_vt = 0;
            $totauxByAppellationsRecap[$key]->volume_sgn = 0;
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
}
