<?php

class DRMGenerateCSV {

    const TYPE_CAVE = 'CAVE';
    const TYPE_CRD = 'CRD';
    const TYPE_ANNEXE = 'ANNEXE';
    const TYPE_ANNEXE_NONAPUREMENT = 'NONAPUREMENT';
    const TYPE_ANNEXE_STATS_EUROPEENES = "STATS-EUROPEENNES";
    // const CONTRATSPRODUITS_NUMERO_CONTRAT = 0;
    // const CONTRATSPRODUITS_ETS_ACHETEUR_ID = 1;
    // const CONTRATSPRODUITS_VOL_TOTAL = 2;
    // const CONTRATSPRODUITS_VOL_ENLEVE = 3;
    // const ETAPE_CHOIX_PRODUITS = 'CHOIX_PRODUITS';
    // const ETAPE_SAISIE = 'SAISIE';
    // const ETAPE_SAISIE_SUSPENDU = 'SAISIE_details';
    // const ETAPE_SAISIE_ACQUITTE = 'SAISIE_detailsACQUITTE';
    // const ETAPE_CRD = 'CRD';
    // const ETAPE_ADMINISTRATION = 'ADMINISTRATION';
    // const ETAPE_VALIDATION = 'VALIDATION';
    // const VALIDE_STATUS_EN_COURS = '';
    // const VALIDE_STATUS_VALIDEE = 'VALIDEE';
    // const VALIDE_STATUS_VALIDEE_ENVOYEE = 'ENVOYEE';
    // const VALIDE_STATUS_VALIDEE_RECUE = 'RECUE';
    // const DRM_VERT = 'VERT';
    // const DRM_BLEU = 'BLEU';
    // const DRM_LIEDEVIN = 'LIEDEVIN';
    // const DRM_DOCUMENTACCOMPAGNEMENT_DAADAC = 'DAADAC';
    // const DRM_DOCUMENTACCOMPAGNEMENT_DAE = 'DAE';
    // const DRM_DOCUMENTACCOMPAGNEMENT_DSADSAC = 'DSADSAC';
    // const DRM_DOCUMENTACCOMPAGNEMENT_EMPREINTE = 'EMPREINTE';
    // const DRM_TYPE_MVT_ENTREES = 'entrees';
    // const DRM_TYPE_MVT_SORTIES = 'sorties';
    // const DRM_CREATION_EDI = 'CREATION_EDI';
    // const DRM_CREATION_VIERGE = 'CREATION_VIERGE';
    // const DRM_CREATION_NEANT = 'CREATION_NEANT';
    // const DRM_CREATION_DOCUMENTS = 'CREATION_DOCUMENTS';
    // const DETAIL_EXPORT_PAYS_DEFAULT = 'inconnu';
    // const TYPE_DRM_SUSPENDU = 'SUSPENDU';
    // const TYPE_DRM_ACQUITTE = 'ACQUITTE';
    //
     const REPRISE_DOC_DR = "DR";
     const REPRISE_DOC_DS = "DS";
     const REPRISE_DOC_VRAC = "VRAC";

     const REPRISE_TYPE_CATALOGUE = "REPRISE_CATALOGUE";
     const REPRISE_TYPE_MOUVEMENT = "REPRISE_MOUVEMENT";
    //

    //
    // public static $types_libelles = array(DRM::DETAILS_KEY_SUSPENDU => 'Suspendu', DRM::DETAILS_KEY_ACQUITTE => 'Acquitté');
    // public static $drm_etapes = array(self::ETAPE_CHOIX_PRODUITS, self::ETAPE_SAISIE_SUSPENDU, self::ETAPE_SAISIE_ACQUITTE, self::ETAPE_CRD, self::ETAPE_ADMINISTRATION, self::ETAPE_VALIDATION);
    // public static $drm_crds_couleurs = array(self::DRM_VERT => 'Vert', self::DRM_BLEU => 'Bleu', self::DRM_LIEDEVIN => 'Lie de vin');
    // public static $drm_max_favoris_by_types_mvt = array(self::DRM_TYPE_MVT_ENTREES => 3, self::DRM_TYPE_MVT_SORTIES => 6);
    // public static $drm_documents_daccompagnement = array(
    //     self::DRM_DOCUMENTACCOMPAGNEMENT_DAADAC => 'DAA/DAC',
    //     self::DRM_DOCUMENTACCOMPAGNEMENT_DSADSAC => 'DSA/DSAC',
    //     self::DRM_DOCUMENTACCOMPAGNEMENT_DAE => 'DAE',
    //     self::DRM_DOCUMENTACCOMPAGNEMENT_EMPREINTE => 'Empreinte');
    // public static $typesCreationLibelles = array(self::DRM_CREATION_DOCUMENTS => "Création d'une drm pré-remplie",
    //                                              self::DRM_CREATION_VIERGE => "Création d'une drm vierge",
    //                                              self::DRM_CREATION_NEANT => "Création d'une drm à néant",
    //                                              self::DRM_CREATION_EDI => 'Création depuis un logiciel tiers');
    // protected $drm_historiques = array();

    protected $identifiant;
    protected $numero_accise;
    protected $periode;
    protected $periode_date;
    protected $aggregate = false;
    protected $firstDrm = false;
    protected $withLieuDit = false;


    public function __construct($identifiant, $numero_accise, $periode, $aggregate = false, $firstDrm = false, $withLieuDit = false){
      $this->identifiant = $identifiant;
      $this->numero_accise = $numero_accise;
      $this->periode =  $periode;
      $this->periode_date = (preg_match('/^[0-9]{6}$/', $periode))? substr($periode, 0, 4)."-".substr($periode, -2)."-01" : date('Y-m-d');
      $this->aggregate = $aggregate;
      $this->firstDrm = $firstDrm;
      $this->withLieuDit = $withLieuDit;
    }

    public function getPeriode(){
      return $this->periode;
    }

    public function getDocumentsForRepriseCatalogue(){
        $documents = array();
        $annee = ConfigurationClient::getInstance()->getAnnee($this->periode);
        $mois = intval(substr($this->periode,4,2));

        $prev_dr = $this->getPreviousDr($this->identifiant, $this->periode);
        $prev_ds = $this->getPreviousDs($this->identifiant, $annee, $mois);
	
        $reprise_dr = false;
        if($prev_dr && (!$prev_ds || (!is_array($prev_ds) && ($prev_dr->getCampagne()."10" > $prev_ds->getPeriode())))){
          $drReprise = $this->createRepriseInfo(self::REPRISE_DOC_DR,self::REPRISE_TYPE_CATALOGUE,$prev_dr->_id);
          $documents[] = $drReprise;
          $reprise_dr = true;
        }elseif($prev_dr && is_array($prev_ds)){
          foreach ($prev_ds as $ds) {
            if($prev_dr->getCampagne()."10" > $ds->getPeriode()){
              $drReprise = $this->createRepriseInfo(self::REPRISE_DOC_DR,self::REPRISE_TYPE_CATALOGUE, $prev_dr->_id);
              $documents[] = $drReprise;
              $reprise_dr = true;
            }
          }
        }
        if(!$reprise_dr && $prev_ds){
          if(is_array($prev_ds)){
            foreach ($prev_ds as $ds) {
              $dsReprise = $this->createRepriseInfo(self::REPRISE_DOC_DS,self::REPRISE_TYPE_CATALOGUE, $ds->_id);
              $documents[] = $dsReprise;
            }
          }else{
            $dsReprise = $this->createRepriseInfo(self::REPRISE_DOC_DS,self::REPRISE_TYPE_CATALOGUE,$prev_ds->_id);
            $documents[] = $dsReprise;
          }
        }
        return $documents;
    }

    public function getDocumentsForRepriseMouvements(){
        $documents = array();
        $reprisesMvtInfos = DRMRepriseMvtsView::getInstance()->getRepriseMvts('ETABLISSEMENT-'.$this->identifiant, $this->periode);
        foreach ($reprisesMvtInfos as $mvtInfo) {
          $mvtInfoSuppl = ($mvtInfo->key[DRMRepriseMvtsView::KEY_TYPE_DOC] == self::REPRISE_DOC_VRAC)? $mvtInfo : null;
          $reprise = $this->createRepriseInfo($mvtInfo->key[DRMRepriseMvtsView::KEY_TYPE_DOC],
                                              self::REPRISE_TYPE_MOUVEMENT,
                                              $mvtInfo->key[DRMRepriseMvtsView::KEY_ID_DOC],
                                              $mvtInfoSuppl);
          $documents[] = $reprise;
        }
        return $documents;
    }

    public function createRowMouvementProduitDetail($produit, $catMouvement,$typeMouvement,$volume, $num_contrat = null){
      $debutLigne = self::TYPE_CAVE . ";" . $this->periode . ";" . $this->identifiant . ";" . $this->numero_accise . ";";
      $lignes = $debutLigne . $this->getProduitCSV($produit,'suspendu') . ";" . $catMouvement.";".$typeMouvement.";".$volume.";";
      $lignes .= ($num_contrat)? ";".str_replace("VRAC-","",$num_contrat).";" : "";
      $lignes .= "\n";
      return $lignes;
    }

    private function getPreviousDr($identifiant, $periode){
      $all_prev_dr = DRClient::getInstance()->getAllByCvi($identifiant);

      if(!$all_prev_dr){
        return null;
      }
      $drArray = $all_prev_dr->getDatas();
      $drArrayReverse = array_reverse($drArray);
      foreach ($drArrayReverse as $prev_dr) {
       if(!$prev_dr->exist('validee') || preg_replace('/([0-9]+)-([0-9]+)-([0-9]+)/', '\1\2', $prev_dr->validee) > $periode) {
               continue;
       }
        $a = substr(strrchr($prev_dr->_id, "-"), 1);
        $annee = substr($periode,0,4);
        $mois = intval(substr($periode,4,2));
        if(($a == $annee ) && ($mois > 9)){
          return $prev_dr;
        }elseif($a == ($annee-1)){
           return $prev_dr;
        }
      }
      return end($drArray);
    }

    private function getPreviousDs($identifiant, $annee,$mois){
      $acCouchdbClientDS = acCouchdbManager::getClient('DS');
      $all_prev_ds = $acCouchdbClientDS->startkey('DS-'.$identifiant.'-000000-000')->endkey('DS-'.$identifiant.'-999999-999')->execute();
      if(!$all_prev_ds){
        return null;
      }
      $allDs = array();
      foreach ($all_prev_ds as $prev_ds) {
        $matches = array();
        preg_match("/([0-9]{4})([0-9]{2})/",$prev_ds->getPeriode(),$matches);
        if((($matches[1] == $annee) && ($matches[2] < $mois)) || ($matches[1] > $annee)){
          $allDs[] = $prev_ds;
        }
      }
      if(count($allDs)){
        return $allDs;
      }
      $arrayDs = $all_prev_ds->getDatas();
      return end($arrayDs);
    }

    private function createRepriseInfo($docType,$repriseType,$idDoc, $viewResult = null){
      $infos = new stdClass();
      $infos->docType = $docType;
      $infos->repriseType = $repriseType;
      $infos->idDoc = $idDoc;
      $infos->viewResult = $viewResult;
      $infos->periode = $this->periode;
      $infos->identifiant = $this->identifiant;
      $infos->numero_accise = $this->numero_accise;
      return $infos;
    }

    public function isProduitDetailAggregate($produitDetail) {
        if(!$this->aggregate) {

            return false;
        }

        if(is_string($produitDetail)) {

            return false;
        }

        $hashProduit = HashMapper::convert($produitDetail->getHash());

        if($produitDetail instanceof ConfigurationCepage) {
            $hashProduit = $produitDetail->getHash();
        }

        if(preg_match('#('.$this->aggregate.')#', $hashProduit)) {

            return true;
        }

        return false;
    }

    public function isProduitDetailWithLieuDit($produitDetail) {
        if(!$this->withLieuDit) {

            return false;
        }

        if(is_string($produitDetail)) {

            return false;
        }

        $hashProduit = HashMapper::convert($produitDetail->getHash());

        if($produitDetail instanceof ConfigurationCepage) {
            $hashProduit = $produitDetail->getHash();
        }

        if(preg_match('#('.$this->withLieuDit.')#', $hashProduit)) {

            return true;
        }

        return false;
    }

    public function createRowStockNullProduit($produitDetail){
        if($this->isProduitDetailAggregate($produitDetail)) {
            return null;
        }
      $debutLigne = self::TYPE_CAVE . ";" . $this->periode . ";" . $this->identifiant . ";" . $this->numero_accise . ";";
      $lignes = $debutLigne . $this->getProduitCSV($produitDetail,'suspendu') . ";" . "stocks_debut;initial;;\n";
      $lignes.= $debutLigne . $this->getProduitCSV($produitDetail,'suspendu') . ";" . "stocks_fin;final;;\n";
      return $lignes;
    }


        public function createRowStockProduitFromDS($produitDetail,$withVolume = false){

            if(!$withVolume && !$this->firstDrm) {
                return;
            }

      $debutLigne = self::TYPE_CAVE . ";" . $this->periode . ";" . $this->identifiant . ";" . $this->numero_accise . ";";
      $produitCepage = $produitDetail->getParent()->getParent();
      $lignes = "";
      if($produitDetail->volume_normal){
        $lignes.= $debutLigne . $this->getProduitCSV($produitDetail,'suspendu') . ";" . "stocks_debut;initial;";
        if($withVolume){
          $lignes.=$produitDetail->volume_normal;
        }
        $lignes.=";\n";
        $lignes.= $debutLigne . $this->getProduitCSV($produitDetail,'suspendu') . ";" . "stocks_fin;final;";
        if($withVolume){
          $lignes.=$produitDetail->volume_normal;
        }
        $lignes.=";\n";
      }
      if($produitDetail->volume_sgn){
        $lignes.= $debutLigne . $this->getProduitCSV($produitDetail,'suspendu','SGN') . ";" . "stocks_debut;initial;";
        if($withVolume){
          $lignes.=$produitDetail->volume_sgn;
        }
        $lignes.=";\n";
        $lignes.= $debutLigne . $this->getProduitCSV($produitDetail,'suspendu','SGN') . ";" . "stocks_fin;final;";
        if($withVolume){
          $lignes.=$produitDetail->volume_sgn;
        }
        $lignes.=";\n";
      }
      if($produitDetail->volume_vt){
        $lignes.= $debutLigne . $this->getProduitCSV($produitDetail,'suspendu','VT') . ";" . "stocks_debut;initial;";
        if($withVolume){
          $lignes.=$produitDetail->volume_vt;
        }
        $lignes.=";\n";
        $lignes.= $debutLigne . $this->getProduitCSV($produitDetail,'suspendu','VT') . ";" . "stocks_fin;final;";
        if($withVolume){
          $lignes.=$produitDetail->volume_vt;
        }
        $lignes.=";\n";

      }
      return $lignes;

    }

    public function createRowStockProduitAutreFromDS($produitDetail,$volume){
      $debutLigne = self::TYPE_CAVE . ";" . $this->periode . ";" . $this->identifiant . ";" . $this->numero_accise . ";";
      $lignes = "";
      $lignes .= $debutLigne . $this->getProduitCSV($produitDetail,'suspendu') . ";" . "stocks_debut;initial;".$volume.";\n";
      $lignes .= $debutLigne . $this->getProduitCSV($produitDetail,'suspendu') . ";" . "stocks_fin;final;".$volume.";\n";
      return $lignes;
    }


    public function getProduitCSV($produitDetail, $force_type_drm = null, $mentionVtsgn = null) {
        $cepageConfig = null;

        if(!is_string($produitDetail)) {
            $hashProduit = HashMapper::convert($produitDetail->getCepage()->getHash());
        }

        if(is_string($produitDetail) && ConfigurationClient::getInstance()->getConfiguration($this->periode_date)->exist($produitDetail)) {
            $cepageConfig = ConfigurationClient::getInstance()->getConfiguration($this->periode_date)->get($produitDetail);
            $produitDetail = $cepageConfig;
            $hashProduit = $cepageConfig->getHash();
        }

        if($this->isProduitDetailAggregate($produitDetail)) {
           try {
               $cepageConfig = $produitDetail->getCepage()->getConfig()->getParent()->get('DEFAUT');
            } catch(Exception $e) {
               $cepageConfig = ConfigurationClient::getInstance()->getConfiguration($this->periode_date)->get($hashProduit)->getParent()->get('DEFAUT');
           }
       }

        if(is_string($produitDetail) && !$cepageConfig) {
            $cepageConfig = ConfigurationClient::getConfiguration($this->periode_date)->identifyProductByLibelle($produitDetail);
        }

        if(!$cepageConfig) {
            $cepageConfig = $produitDetail->getCepage()->getConfig();
        }
        $certification = $cepageConfig->getCertification()->getLibelle();
        $genre = $cepageConfig->getGenre()->getLibelle();
        $appellation = $cepageConfig->getAppellation()->getLibelle();
        $mention = $cepageConfig->getMention()->getLibelle();
        if($mentionVtsgn){
          $mention = $mentionVtsgn;
        }
        $lieu = $cepageConfig->getLieu()->getLibelle();
        $couleur = $cepageConfig->getCouleur()->getLibelle();
        $cepage = $cepageConfig->getCepage()->getLibelle();

        $complement = "";
        $libelle = $cepageConfig->getLibelleFormat();

        if($cepageConfig->getGenre()->getKey() == 'EFF' && strpos($cepage, "Rosé") !== false) {
            $certification = "";
            $appellation = "";
            $cepage = "";
            $libelle = $cepageConfig->getLieu()->getLibelleFormat()." Rosé";
        } elseif($cepageConfig->getGenre()->getKey() == 'EFF') {
            $certification = "";
            $appellation = "";
            $cepage = "";
            $libelle = $cepageConfig->getLieu()->getLibelleFormat()." Blanc";
        }

        if($mention) {
            $libelle .= " ".$mention;
        }

	    if($cepageConfig->hasLieuEditable() && $this->isProduitDetailWithLieuDit($produitDetail) && $produitDetail->lieu) {
	        $complement = $produitDetail->lieu;
        }

        if($produitDetail instanceof DSDetail){
          $libelle = str_ireplace("Vins sans IG Sans IG","Vins sans IG Blanc",$libelle);
          $libelle = preg_replace("/^Vins sans IG Mousseux$/","Vins sans IG Mousseux Blanc",$libelle);
        }

        $type_drm = 'suspendu';
        if($force_type_drm){
          $type_drm = $force_type_drm;
        }
        return $certification . ";" .
         $genre . ";" .
          $appellation .
          ";" . $mention .
          ";" . $lieu .
          ";" . $couleur .
           ";" . $cepage.
           ";". $complement.
            ";". $libelle.
            ";". $type_drm;
    }

}
