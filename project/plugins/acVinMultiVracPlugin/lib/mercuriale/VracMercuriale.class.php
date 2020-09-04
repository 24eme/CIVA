<?php
class VracMercuriale
{
    CONST CSV_FILE_NAME = 'datas_mercuriale.csv';
	CONST IN_DATE = 0;
	CONST IN_VISA = 1;
	CONST IN_MERCURIAL = 2;
	CONST IN_CP_CODE = 3;
	CONST IN_CP_LIBELLE = 4;
	CONST IN_VOL = 5;
	CONST IN_PRIX = 6;
	CONST IN_BIO = 7;
    CONST IN_VRAC_ID = 8;
    CONST IN_ORDRE = 9;
    CONST IN_TYPE_CONTRAT = 10;
    CONST IN_MONTANT_COTISATION = 11;
    CONST IN_MONTANT_COTISATION_PAYE = 12;
    CONST IN_MODE_DE_PAIEMENT = 13;
    CONST IN_CVI_ACHETEUR = 14;
    CONST IN_TYPE_ACHETEUR = 15;
    CONST IN_TCA = 16;
    CONST IN_CVI_VENDEUR = 17;
    CONST IN_TYPE_VENDEUR = 18;
    CONST IN_NUMERO_CONTRAT = 19;
    CONST IN_DAA = 20;
    CONST IN_DATE_ARRIVEE = 21;
    CONST IN_DATE_SAISIE = 22;
    CONST IN_IDENTIFIANT_COURTIER = 23;
    CONST IN_RECCOD = 24;
    CONST IN_TOTAL_VOLUME_PROPOSE = 25;
    CONST IN_TOTAL_VOLUME_ENLEVE = 26;
    CONST IN_QUANTITE_TRANSFEREE = 27;
    CONST IN_TOP_SUPPRESSION = 28;
    CONST IN_TOP_INSTANCE = 29;
    CONST IN_NOMBRE_CONTRATS = 30;
    CONST IN_HEURE_TRAITEMENT = 31;
    CONST IN_UTILISATEUR = 32;
    CONST IN_DATE_MODIF = 33;
    CONST IN_CREATION = 34;

	CONST OUT_MERCURIALE = "MERCURIALE";
	CONST OUT_STATS = "STATISTIQUES";
	CONST OUT_CP_CODE = "CEPAGE_CODE";
	CONST OUT_CP_LIBELLE = "CEPAGE_LIBELLE";
	CONST OUT_VOL = "VOLUME";
	CONST OUT_PRIX = "PRIX";
	CONST OUT_VOL_PERC = "EVOLUTION_VOLUME";
	CONST OUT_PRIX_PERC = "EVOLUTION_PRIX";
	CONST OUT_MIN = "PRIX_MIN";
	CONST OUT_MAX = "PRIX_MAX";
	CONST OUT_NB = "NOMBRE";
	CONST OUT_CONTRAT = "CONTRAT";
	CONST OUT_START = "START";
	CONST OUT_END = "END";
	CONST OUT_CAMPAGNE = "CAMPAGNE";
	CONST OUT_CURRENT = "DONNEES_COURANTE";
	CONST OUT_PREVIOUS = "DONNEES_PRECEDENTE";
	CONST OUT_VARIATION = "VARIATION";
	CONST OUT_VISA = "VISA";
	CONST OUT_BIO = "BIO";
    CONST OUT_VRAC_ID = "VRAC_ID";
    CONST OUT_ORDRE = "ORDRE";
    CONST OUT_TYPE_CONTRAT = "TYPE_CONTRAT";
    CONST OUT_MONTANT_COTISATION = "MONTANT_COTISATION";
    CONST OUT_MONTANT_COTISATION_PAYE = "MONTANT_COTISATION_PAYE";
    CONST OUT_MODE_DE_PAIEMENT = "MODE_DE_PAIEMENT";
    CONST OUT_CVI_ACHETEUR = "CVI_ACHETEUR";
    CONST OUT_TYPE_ACHETEUR = "TYPE_ACHETEUR";
    CONST OUT_TCA = "TCA";
    CONST OUT_CVI_VENDEUR = "CVI_VENDEUR";
    CONST OUT_TYPE_VENDEUR = "TYPE_VENDEUR";
    CONST OUT_NUMERO_CONTRAT = "NUMERO_CONTRAT";
    CONST OUT_DAA = "DAA";
    CONST OUT_DATE_ARRIVEE = "DATE_ARRIVEE";
    CONST OUT_DATE_SAISIE = "DATE_SAISIE";
    CONST OUT_IDENTIFIANT_COURTIER = "IDENTIFIANT_COURTIER";
    CONST OUT_RECCOD = "RECCOD";
    CONST OUT_TOTAL_VOLUME_PROPOSE = "TOTAL_VOLUME_PROPOSE";
    CONST OUT_TOTAL_VOLUME_ENLEVE = "TOTAL_VOLUME_ENLEVE";
    CONST OUT_QUANTITE_TRANSFEREE = "QUANTITE_TRANSFEREE";
    CONST OUT_TOP_SUPPRESSION = "TOP_SUPPRESSION";
    CONST OUT_TOP_INSTANCE = "TOP_INSTANCE";
    CONST OUT_NOMBRE_CONTRATS = "NOMBRE_CONTRATS";
    CONST OUT_HEURE_TRAITEMENT = "HEURE_TRAITEMENT";
    CONST OUT_UTILISATEUR = "UTILISATEUR";
    CONST OUT_DATE_MODIF = "DATE_MODIF";
    CONST OUT_CREATION = "CREATION";

	CONST NB_MIN_TO_AGG = 3;

	public static $cepages = array(
			'ED' => 'Edelzwicker',
			'GW' => 'Gewurztraminer',
			'MU' => 'Muscat',
			'PB' => 'Pinot Blanc',
			'PG' => 'Pinot Gris',
			'PN' => 'Pinot Noir',
			'RI' => 'Riesling',
			'SY' => 'Sylvaner / CH',
			'CR' => 'Cremant',
	);
	public static $ordres = array(
			'ED' => 2,
			'GW' => 7,
			'MU' => 6,
			'PB' => 3,
			'PG' => 5,
			'PN' => 8,
			'RI' => 4,
			'SY' => 1,
			'CR' => 9,
	);
	
	protected $datas;
	
	protected $folderPath;
	protected $publicPdfPath;
	protected $pdfFilename;
	protected $csvFilename;
	protected $start;
	protected $end;
	protected $mercuriale;
	protected $context;
	protected $allContrats;
	protected $allLots;
	protected $allContratsBio;
	protected $allLotsBio;

	public function __construct($folderPath, $start = null, $end = null, $filtres = null, $publicPdfPath = null)
	{
		$this->folderPath = $folderPath;
		if (!is_dir($this->folderPath)) {
		    mkdir($this->folderPath, 0770);
		}
		if (!is_dir($this->folderPath)) {
		    throw new sfException($this->folderPath." n'a pas pu être créé");
		}
		$this->publicPdfPath = ($publicPdfPath)? $publicPdfPath : sfConfig::get('sf_data_dir').'/mercuriales/pdf/';
		if (!is_dir($this->publicPdfPath)) {
		    mkdir($this->publicPdfPath, 0770);
		}
		if (!is_dir($this->publicPdfPath)) {
		    throw new sfException($this->publicPdfPath." n'a pas pu être créé");
		}
		$csvFile = $this->folderPath.self::CSV_FILE_NAME;
		$this->generateMercurialeDatasFile($csvFile);
		$this->datas = $this->getDatasFromCsvFile($csvFile);
		$this->start = $this->getDate($start);
		$this->end = $this->getDate($end);
        if ($filtres) {
            if (is_string($filtres)) {
                $filtres = array($filtres);
            }
            $this->mercuriale = array_diff($filtres, array('CR'));
            $this->mercuriale = ($this->mercuriale) ? implode('_', $this->mercuriale) : null;
            $this->filtres = implode('_', $filtres);
        }else{
            $this->filtres = null;
            $this->mercuriale = null;
        }
		$this->pdfFilename = ($this->filtres)? $this->start.'_'.$this->end.'_'.$this->filtres.'_mercuriales.pdf' : $this->start.'_'.$this->end.'_mercuriales.pdf';
		$this->csvFilename = ($this->filtres)? $this->start.'_'.$this->end.'_'.$this->filtres.'_mercuriales.csv' : $this->start.'_'.$this->end.'_mercuriales.csv';
		$this->context = null;
		$this->allContrats = array();
		$this->allLots = array();
		$this->allContratsBio = array();
		$this->allLotsBio = array();
	}

	public function getAllContrats($isbio = 0)
	{
        if ($isbio) {
            return $this->getAllContratsBio();
        }
        return $this->allContrats;
	}

	public function getAllLots($isbio = 0)
	{
        if ($isbio) {
            return $this->getAllLotsBio();
        }
	    return $this->allLots;
	}

	public function getAllContratsBio()
	{
	    return $this->allContratsBio;
	}

	public function getAllLotsBio()
	{
	    return $this->allLotsBio;
	}

	public function getFolderPath()
	{
	    return $this->folderPath;
	}

	public function getPublicPdfPath()
	{
	    return $this->publicPdfPath;
	}

	public function getPdfFilname()
	{
	    return $this->pdfFilename;
	}

	public function getStart($f = 'd/m/Y')
	{
	    $dt = new DateTime(substr($this->start, 0, 4).'-'.substr($this->start, 5, 2).'-'.substr($this->start, -2));
	    return $dt->format($f);
	}

	public function getEnd($f = 'd/m/Y')
	{
	    $dt = new DateTime($this->getCumulPeriodesRange('currentPeriodeEnd'));
	    return $dt->format($f);
	}
    public function getBegin($f = 'd/m/Y')
	{
	    $dt = new DateTime($this->getCumulPeriodesRange('currentPeriodeBegin'));
	    return $dt->format($f);
	}
    public function getEndPrevious($f = 'd/m/Y')
	{
	    $dt = new DateTime($this->getCumulPeriodesRange('previousPeriodeEnd'));
	    return $dt->format($f);
	}
    public function getBeginPrevious($f = 'd/m/Y')
	{
	    $dt = new DateTime($this->getCumulPeriodesRange('previousPeriodeBegin'));
	    return $dt->format($f);
	}

	public function setContext($context) {
		$this->context = $context;
	}

	protected function getDate($date)
	{
		if (!$date) {
			return null;
        }
		if (!preg_match('/^[0-9\-]{10}$/', $date)) {
			throw new sfException($date.' format not valid');
		}
		return $date;
	}

	// *** BEGIN GENERATE DATAS FCTS ***

	public function generateMercurialeDatasFile($csvFile) {
		if (file_exists($csvFile)) {
			return;
		}
		$items = $this->getMercurialeDatas();
		$csv = new ExportCsv(array('#DATE',
                        self::OUT_VISA,
                        self::OUT_MERCURIALE,
                        self::OUT_CP_CODE,
                        self::OUT_CP_LIBELLE,
                        self::OUT_VOL,
                        self::OUT_PRIX,
                        self::OUT_BIO,
                        self::OUT_VRAC_ID,
                        self::OUT_ORDRE,
                        self::OUT_TYPE_CONTRAT,
                        self::OUT_MONTANT_COTISATION,
                        self::OUT_MONTANT_COTISATION_PAYE,
                        self::OUT_MODE_DE_PAIEMENT,
                        self::OUT_CVI_ACHETEUR,
                        self::OUT_TYPE_ACHETEUR,
                        self::OUT_TCA,
                        self::OUT_CVI_VENDEUR,
                        self::OUT_TYPE_VENDEUR,
                        self::OUT_NUMERO_CONTRAT,
                        self::OUT_DAA,
                        self::OUT_DATE_ARRIVEE,
                        self::OUT_DATE_SAISIE,
                        self::OUT_IDENTIFIANT_COURTIER,
                        self::OUT_RECCOD,
                        self::OUT_TOTAL_VOLUME_PROPOSE,
                        self::OUT_TOTAL_VOLUME_ENLEVE,
                        self::OUT_QUANTITE_TRANSFEREE,
                        self::OUT_TOP_SUPPRESSION,
                        self::OUT_TOP_INSTANCE,
                        self::OUT_NOMBRE_CONTRATS,
                        self::OUT_HEURE_TRAITEMENT,
                        self::OUT_UTILISATEUR,
                        self::OUT_DATE_MODIF,
                        self::OUT_CREATION
                    ), "\r\n");
		foreach ($items as $date => $values) {
			foreach ($values as $result) {
				$csv->add(array(
                    self::convertDate($date),
                    $result[self::OUT_VISA],
                    $result[self::OUT_MERCURIALE],
                    $result[self::OUT_CP_CODE],
                    $result[self::OUT_CP_LIBELLE],
                    number_format($result[self::OUT_VOL]*1, 2, '.', ''),
                    number_format($result[self::OUT_PRIX]*1, 2, '.', ''),
                    $result[self::OUT_BIO],
                    $result[self::OUT_VRAC_ID],
                    $result[self::OUT_ORDRE],
                    $result[self::OUT_TYPE_CONTRAT],
                    $result[self::OUT_MONTANT_COTISATION],
                    $result[self::OUT_MONTANT_COTISATION_PAYE],
                    $result[self::OUT_MODE_DE_PAIEMENT],
                    $result[self::OUT_CVI_ACHETEUR],
                    $result[self::OUT_TYPE_ACHETEUR],
                    $result[self::OUT_TCA],
                    $result[self::OUT_CVI_VENDEUR],
                    $result[self::OUT_TYPE_VENDEUR],
                    $result[self::OUT_NUMERO_CONTRAT],
                    $result[self::OUT_DAA],
                    $result[self::OUT_DATE_ARRIVEE],
                    $result[self::OUT_DATE_SAISIE],
                    $result[self::OUT_IDENTIFIANT_COURTIER],
                    $result[self::OUT_RECCOD],
                    number_format($result[self::OUT_TOTAL_VOLUME_PROPOSE]*1, 2, '.', ''),
                    number_format($result[self::OUT_TOTAL_VOLUME_ENLEVE]*1, 2, '.', ''),
                    $result[self::OUT_QUANTITE_TRANSFEREE],
                    $result[self::OUT_TOP_SUPPRESSION],
                    $result[self::OUT_TOP_INSTANCE],
                    $result[self::OUT_NOMBRE_CONTRATS],
                    $result[self::OUT_HEURE_TRAITEMENT],
                    $result[self::OUT_UTILISATEUR],
                    $result[self::OUT_DATE_MODIF],
                    $result[self::OUT_CREATION]
                ));
			}
		}
		file_put_contents($csvFile, $csv->output());
	}

	protected function getMercurialeDatas($from = '1990-01-01', $to = null) {
		$items = array();
		$to = ($to)? $to :  date('Y-m-d', time() - 3600 * 24);
		$contrats = VracContratsView::getInstance()->findForDb2Export(array($from, $to));
		foreach($contrats as $contrat) {
			if ($date = self::convertDate($contrat->value[VracContratsView::VALUE_DATE_TRAITEMENT])) {
				$mercuriale = $contrat->value[VracContratsView::VALUE_MERCURIALES];
				$produits = VracProduitsView::getInstance()->findForDb2Export($contrat->value[VracContratsView::VALUE_NUMERO_ARCHIVE]);
				foreach ($produits as $produit) {
				    if ($produit->value[VracProduitsView::VALUE_CODE_APPELLATION] == 'GRDCRU') {
				        continue;
				    }
				    if ($produit->value[VracProduitsView::VALUE_CODE_APPELLATION] == 'VINTABLE') {
				        continue;
				    }
				    if ($produit->value[VracProduitsView::VALUE_VTSGN]) {
				        continue;
				    }
					if ($cepage = self::getCepage($produit->value[VracProduitsView::VALUE_CEPAGE], $produit->value[VracProduitsView::VALUE_CODE_APPELLATION])) {
						$volume = ($produit->value[VracProduitsView::VALUE_DATE_CIRCULATION])? $produit->value[VracProduitsView::VALUE_VOLUME_ENLEVE] : $produit->value[VracProduitsView::VALUE_VOLUME_PROPOSE];
						$prix = $produit->value[VracProduitsView::VALUE_PRIX_UNITAIRE] / 100;
						$bio = ($produit->value[VracProduitsView::VALUE_DEGRE] == 99)? 1 : 0;
                        $appellation = $produit->value[VracProduitsView::VALUE_CODE_APPELLATION];
						if (!isset($items[$date])) {
							$items[$date] = array();
						}
						$items[$date][] = array(
                            self::OUT_VISA => $contrat->value[VracContratsView::VALUE_NUMERO_ARCHIVE],
                            self::OUT_MERCURIALE => $mercuriale,
                            self::OUT_CP_CODE => strtoupper($cepage),
                            self::OUT_CP_LIBELLE => strtoupper($this->getCepageLibelle($cepage)),
                            self::OUT_VOL => $volume,
                            self::OUT_PRIX => $prix,
                            self::OUT_BIO => $bio,
                            self::OUT_VRAC_ID => $contrat->id,
                            self::OUT_ORDRE => $this->getOrdre($cepage, $appellation),
                            self::OUT_TYPE_CONTRAT => $contrat->value[VracContratsView::VALUE_TYPE_CONTRAT],
                            self::OUT_MONTANT_COTISATION => $contrat->value[VracContratsView::VALUE_MONTANT_COTISATION],
                            self::OUT_MONTANT_COTISATION_PAYE => $contrat->value[VracContratsView::VALUE_MONTANT_COTISATION_PAYE],
                            self::OUT_MODE_DE_PAIEMENT => $contrat->value[VracContratsView::VALUE_MODE_DE_PAIEMENT],
                            self::OUT_CVI_ACHETEUR => $contrat->value[VracContratsView::VALUE_CVI_ACHETEUR],
                            self::OUT_TYPE_ACHETEUR => $contrat->value[VracContratsView::VALUE_TYPE_ACHETEUR],
                            self::OUT_TCA => $contrat->value[VracContratsView::VALUE_TCA],
                            self::OUT_CVI_VENDEUR => $contrat->value[VracContratsView::VALUE_CVI_VENDEUR],
                            self::OUT_TYPE_VENDEUR => $contrat->value[VracContratsView::VALUE_TYPE_VENDEUR],
                            self::OUT_NUMERO_CONTRAT => $contrat->value[VracContratsView::VALUE_NUMERO_CONTRAT],
                            self::OUT_DAA => $contrat->value[VracContratsView::VALUE_DAA],
                            self::OUT_DATE_ARRIVEE => self::convertDate($contrat->value[VracContratsView::VALUE_DATE_ARRIVEE]),
                            self::OUT_DATE_SAISIE => self::convertDate($contrat->value[VracContratsView::VALUE_DATE_SAISIE]),
                            self::OUT_IDENTIFIANT_COURTIER => $contrat->value[VracContratsView::VALUE_IDENTIFIANT_COURTIER],
                            self::OUT_RECCOD => $contrat->value[VracContratsView::VALUE_RECCOD],
                            self::OUT_TOTAL_VOLUME_PROPOSE => $contrat->value[VracContratsView::VALUE_TOTAL_VOLUME_PROPOSE],
                            self::OUT_TOTAL_VOLUME_ENLEVE => $contrat->value[VracContratsView::VALUE_TOTAL_VOLUME_ENLEVE],
                            self::OUT_QUANTITE_TRANSFEREE => $contrat->value[VracContratsView::VALUE_QUANTITE_TRANSFEREE],
                            self::OUT_TOP_SUPPRESSION => $contrat->value[VracContratsView::VALUE_TOP_SUPPRESSION],
                            self::OUT_TOP_INSTANCE => $contrat->value[VracContratsView::VALUE_TOP_INSTANCE],
                            self::OUT_NOMBRE_CONTRATS => $contrat->value[VracContratsView::VALUE_NOMBRE_CONTRATS],
                            self::OUT_HEURE_TRAITEMENT => $contrat->value[VracContratsView::VALUE_HEURE_TRAITEMENT],
                            self::OUT_UTILISATEUR => $contrat->value[VracContratsView::VALUE_UTILISATEUR],
                            self::OUT_DATE_MODIF => self::convertDate($contrat->value[VracContratsView::VALUE_DATE_MODIF]),
                            self::OUT_CREATION => $contrat->value[VracContratsView::VALUE_CREATION]
                        );
					}
				}
			}
		}
		ksort($items);
		return $items;
	}

    public static function convertDate($s){
        $s = str_replace('-', '', $s);
        return substr($s, 0, 4).'-'.substr($s, 4, 2).'-'.substr($s, 6, 2);
    }

    public static function getCodeAppellation($appellation)
    {
        if($appellation == "VINTABLE") {

            return -1;
        }

    	$code = 1;

    	switch ($appellation) {
                case 'CREMANT':
                    $code = 2;
                    break;
                case 'GRDCRU':
                    $code = 3;
                    break;
             	case "COMMUNALE":
                    $code = 7;
                    break;
             	case "LIEUDIT":
                    $code = 8;
                default:
                    $code = 1;
        }
        return $code;
    }

    public static function getCepage($cepage, $appellation)
    {
    	if ($appellation == 'CREMANT') {
            return "CR";
    	}

        if ($cepage == "AU" || $cepage == "PI") {
            $cepage = "PB";
        }

        if ($cepage == "MO") {
            $cepage = "MU";
        }

        if ($cepage == "PR") {
			$cepage = "PN";
		}
		if ($cepage == "MO") {
			$cepage = "MU";
		}
		if ($cepage == "CH") {
			$cepage = "SY";
		}
		return (in_array($cepage,array_keys(self::$cepages)))? $cepage : null;
	}

	protected function getCepageLibelle($cepage)
	{
		return (isset(self::$cepages[$cepage]))? self::$cepages[$cepage] : $cepage;
	}
	
	protected function getDatasFromCsvFile($csvFile)
	{
		if (strpos($csvFile, ';') === false) {
			return array_map(function($v){ return str_getcsv($v, ";"); }, file($csvFile));
		} else {
			return str_getcsv($csvFile, ";");
		}
	}
	
	// *** END GENERATE DATAS FCTS ***

	protected function getPlotConfig($vars = null)
	{
		$context = ($this->context)? $this->context : sfContext::getInstance();
		return  $context->getController()->getAction('mercuriales', 'vracPlotConfig')->getPartial('mercuriales/vracPlotConfig', $vars);
	}
	
	public function generateMercurialePlotFiles($cepages = array(), $bio = 0)
	{
		if (!count($cepages)) { return; }
		$csvFile = $this->folderPath.'plotdatas_'.implode('_', $cepages).'.csv';
		$confFile = $this->folderPath.'plot_'.implode('_', $cepages).'.conf';
		
		$items = $this->getMercurialePlotDatas($cepages, $bio);
		$csv = new ExportCsv(array_merge(array('#PERIODE', 'XTICS'), $cepages), "\r\n");
		$xtic = 0;
		$currentPeriode = null;
		foreach ($items as $periode => $values) {
			if ($currentPeriode != $periode) {
				if ($currentPeriode) {
					$xtic += 5;
				}
				$currentPeriode = $periode;
			}
			$csv->add(array_merge(array($periode, $xtic),$values));
			
		}
		file_put_contents($csvFile, $csv->output());
		
		file_put_contents($confFile, $this->getPlotConfig(array('csvFile' => $csvFile, 'datas' => $this->getDatasFromCsvFile($csvFile), 'cols' => $cepages, 'file' => str_replace('.conf', '.svg', $confFile))));
	    exec("gnuplot $confFile");
	}
	
	public function getMercurialePlotDatas($cepages = array(), $bio = 0)
	{
		if (!count($this->datas)) { return array(); }
		if (!count($cepages)) { return array(); }
		$result = array();
		foreach ($this->datas as $datas) {
		    if (!$datas[self::IN_BIO] && $bio == 1) {
		        continue;
		    }
		    if ($datas[self::IN_BIO] && $bio == 0) {
		        continue;
		    }
			if (!in_array($datas[self::IN_CP_CODE], $cepages)) {
				continue;
			}
            if ($this->mercuriale !== null && strpos($this->mercuriale, $datas[self::IN_MERCURIAL]) === false) {
                continue;
            }
			$periode = $this->getPlotPeriode($datas[self::IN_DATE]);
			if (!isset($result[$periode])) {
				$result[$periode] = array();
			}
			if (!count($result[$periode])) {
				foreach ($cepages as $cepage) {
					$result[$periode][$cepage] = array();
				}
			}
            $result[$periode][$datas[self::IN_CP_CODE]][] = array(
                'prix' => (float) $datas[self::IN_PRIX] * (float) $datas[self::IN_VOL],
                'volume' => (float) $datas[self::IN_VOL]
            );
		}
		$toMerge = array();
		foreach ($result as $periode => $values) {
			$merge = true;
			foreach ($values as $cepage => $prix) {
				if (count($prix) >= self::NB_MIN_TO_AGG) {
					$merge = false;
					break;
				}
			}
			if ($merge) {
				$toMerge[substr($periode, 0, 6)] = substr($periode, 0, 6).'0';
			}
		}
		foreach ($toMerge as $mois => $periode) {
			if (isset($result[$mois.'1']) && isset($result[$mois.'2'])) {
				$result[$periode] = array_merge_recursive($result[$mois.'1'], $result[$mois.'2']);
				unset($result[$mois.'1']);
				unset($result[$mois.'2']);
			} elseif (isset($result[$mois.'1'])) {
				$result[$periode] = $result[$mois.'1'];
				unset($result[$mois.'1']);
			} elseif (isset($result[$mois.'2'])) {
				$result[$periode] = $result[$mois.'2'];
				unset($result[$mois.'2']);
			}
		}
		ksort($result);
		$datas = array();
		foreach ($result as $periode => $values) {
			foreach ($values as $nom_cepage => $cepage) {
				$nb = count($cepage);
                if ($nb >= self::NB_MIN_TO_AGG) {
                    $total_volume = round(array_sum(array_column($cepage, 'volume')),2);
                    $total_prix = round(array_sum(array_column($cepage, 'prix')),2);
                    $somme_ponderee = $total_prix / $total_volume;
                    $datas[$periode][$nom_cepage] = round($somme_ponderee, 2);
                } else {
                    $datas[$periode][$nom_cepage] = null;
                }
			}
		}
		return $datas;
	}

	protected function getPlotPeriode($date)
	{
        $date = str_replace('-', '', $date);
		return (substr($date, -2) > 15)? (substr($date, 0, 6).'2')*1 : (substr($date, 0, 6).'1')*1;
	}

	public function getOrdre($cep, $appellation)
	{
	    return (isset(self::$ordres[self::getCepage($cep, $appellation)]))? self::$ordres[self::getCepage($cep, $appellation)] : 9;
	}

    public function getCumulPeriodesRanges() {
        $tabDate = array(substr($this->end, 0, 4), substr($this->end, 5, 2), substr($this->end, -2));
        $diff_annee_deb_campagne = ($tabDate[1] == '12') ? 0 : 1;
        $periode = array();
        $periode['currentPeriodeBegin'] = ($tabDate[0] - $diff_annee_deb_campagne).'-12-01';
        $periode['currentPeriodeEnd'] = $tabDate[0].'-'.$tabDate[1].'-'.$tabDate[2];
        $periode['previousPeriodeBegin'] = ($tabDate[0] - 1 - $diff_annee_deb_campagne).'-12-01';
        $periode['previousPeriodeEnd'] = ($tabDate[0] - 1).'-'.$tabDate[1].'-'.$tabDate[2];
        return $periode;
    }
    public function getCumulPeriodesRange($str) {
        $a = $this->getCumulPeriodesRanges();
        return $a[$str];
    }



	public function getCumul($withCR = false, $bio = 0)
	{
	    if (!$this->start && !$this->end) {
	        throw new sfException('period must be setted');
	    }

        $periodesRange = $this->getCumulPeriodesRanges($this->end);

	    $currentPeriode = $this->getStats($periodesRange['currentPeriodeBegin'], $periodesRange['currentPeriodeEnd'], $withCR, $bio);
	    $nbContratsCurrent = ($bio)? count($this->getAllContratsBio()) : count($this->getAllContrats());
	    $nbLotsCurrent = ($bio)? count($this->getAllLotsBio()) : count($this->getAllLots());

	    $previousPeriode = $this->getStats($periodesRange['previousPeriodeBegin'], $periodesRange['previousPeriodeEnd'], $withCR, $bio);
	    $nbContratsPrevious = ($bio)? count($this->getAllContratsBio()) : count($this->getAllContrats());
	    $nbLotsPrevious = ($bio)? count($this->getAllLotsBio()) : count($this->getAllLots());

	    $result[self::OUT_STATS] = array(
	        self::OUT_PREVIOUS => array(self::OUT_NB => $nbLotsPrevious, self::OUT_CONTRAT => $nbContratsPrevious),
	        self::OUT_CURRENT => array(self::OUT_NB => $nbLotsCurrent, self::OUT_CONTRAT => $nbContratsCurrent),
	        self::OUT_VARIATION => array(self::OUT_NB => ($nbLotsCurrent - $nbLotsPrevious), self::OUT_CONTRAT => ($nbContratsCurrent - $nbContratsPrevious))
	    );

	    foreach ($currentPeriode as $k => $datas) {
	       $cep = str_replace('_BIO', '', $k);
	       $ordre = '';
	       $result[$ordre.$cep] = array(
	           self::OUT_CP_CODE => $datas[self::OUT_CP_CODE],
	           self::OUT_CP_LIBELLE => $datas[self::OUT_CP_LIBELLE],
	           self::OUT_CURRENT => array(
	               self::OUT_START => $periodesRange['currentPeriodeBegin'],
	               self::OUT_END => $periodesRange['currentPeriodeEnd'],
	               self::OUT_NB => $datas[self::OUT_NB],
	               self::OUT_CONTRAT => $datas[self::OUT_CONTRAT],
	               self::OUT_VOL => $datas[self::OUT_VOL],
	               self::OUT_PRIX => $datas[self::OUT_PRIX]),
	           self::OUT_PREVIOUS => array(
	               self::OUT_START => $periodesRange['previousPeriodeBegin'],
	               self::OUT_END => $periodesRange['previousPeriodeEnd'],
	               self::OUT_NB => 0,
	               self::OUT_CONTRAT => 0,
	               self::OUT_VOL => number_format(0, 2, '.', ''),
	               self::OUT_PRIX => number_format(0, 2, '.', ''))
	       );
	       $result[$ordre.$cep][self::OUT_VARIATION] = array(
	           self::OUT_NB => $result[$ordre.$cep][self::OUT_CURRENT][self::OUT_NB],
	           self::OUT_CONTRAT => $result[$ordre.$cep][self::OUT_CURRENT][self::OUT_CONTRAT],
	           self::OUT_VOL => $result[$ordre.$cep][self::OUT_CURRENT][self::OUT_VOL] * 1,
	           self::OUT_PRIX => $result[$ordre.$cep][self::OUT_CURRENT][self::OUT_PRIX] * 1,
	           self::OUT_VOL_PERC => 100,
	           self::OUT_PRIX_PERC => 100,
	       );
	    }
	    foreach ($previousPeriode as $k => $datas) {
	       $cep = str_replace('_BIO', '', $k);
	       $ordre = '';
	        if (!isset($result[$ordre.$cep])) {
	           $result[$ordre.$cep] = array(
	                self::OUT_CP_CODE => $datas[self::OUT_CP_CODE],
	                self::OUT_CP_LIBELLE => $datas[self::OUT_CP_LIBELLE],
	                self::OUT_PREVIOUS => array(
	                    self::OUT_START => $periodesRange['previousPeriodeBegin'],
	                    self::OUT_END => $periodesRange['previousPeriodeEnd'],
	                    self::OUT_NB => $datas[self::OUT_NB],
	                    self::OUT_CONTRAT => $datas[self::OUT_CONTRAT],
	                    self::OUT_VOL => $datas[self::OUT_VOL],
	                    self::OUT_PRIX => $datas[self::OUT_PRIX]),
	                self::OUT_CURRENT => array(
	                    self::OUT_START => $periodesRange['currentPeriodeBegin'],
	                    self::OUT_END => $periodesRange['currentPeriodeEnd'],
	                    self::OUT_NB => 0,
	                    self::OUT_CONTRAT => 0,
	                    self::OUT_VOL => number_format(0, 2, '.', ''),
	                    self::OUT_PRIX => number_format(0, 2, '.', ''))
	            );
	        } else {
	            $result[$ordre.$cep][self::OUT_PREVIOUS][self::OUT_NB] = $datas[self::OUT_NB];
	            $result[$ordre.$cep][self::OUT_PREVIOUS][self::OUT_CONTRAT] = $datas[self::OUT_CONTRAT];
	            $result[$ordre.$cep][self::OUT_PREVIOUS][self::OUT_VOL] = $datas[self::OUT_VOL];
	            $result[$ordre.$cep][self::OUT_PREVIOUS][self::OUT_PRIX] = $datas[self::OUT_PRIX];
	        }
	        $varNb = ($result[$ordre.$cep][self::OUT_CURRENT][self::OUT_NB]) - ($result[$ordre.$cep][self::OUT_PREVIOUS][self::OUT_NB]);
	        $varContrat = ($result[$ordre.$cep][self::OUT_CURRENT][self::OUT_CONTRAT]) - ($result[$ordre.$cep][self::OUT_PREVIOUS][self::OUT_CONTRAT]);
	        $varVol = ($result[$ordre.$cep][self::OUT_CURRENT][self::OUT_VOL] * 1) - ($result[$ordre.$cep][self::OUT_PREVIOUS][self::OUT_VOL] * 1);
	        $varPrix = ($result[$ordre.$cep][self::OUT_CURRENT][self::OUT_PRIX] * 1) - ($result[$ordre.$cep][self::OUT_PREVIOUS][self::OUT_PRIX] * 1);
	        $varVolPerc = round(($varVol * 100) / ($result[$ordre.$cep][self::OUT_PREVIOUS][self::OUT_VOL] * 1));
	        $varPrixPerc = round(($varPrix * 100) / ($result[$ordre.$cep][self::OUT_PREVIOUS][self::OUT_PRIX] * 1));

	        $result[$ordre.$cep][self::OUT_VARIATION] = array(
	            self::OUT_NB => $varNb,
	            self::OUT_CONTRAT => $varContrat,
	            self::OUT_VOL => number_format($varVol, 2, '.', ''),
	            self::OUT_PRIX => number_format($varPrix, 2, '.', ''),
	            self::OUT_VOL_PERC => ($varVolPerc)? $varVolPerc : 0,
	            self::OUT_PRIX_PERC => ($varPrixPerc)? $varPrixPerc : 0,
	        );
	    }
	    ksort($result);
	    return $result;
	}

    public function hasWithCremant() {
        return preg_match('/CR/', $this->filtres);
    }

	public function getStats($start = null, $end = null, $withCR = false, $bio = 0)
	{
	    if (!$start) {
	        $start = $this->start;
	    } else {
	        $start = $this->getDate($start);
	    }
	    if (!$end) {
	        $end = $this->end;
	    } else {
	        $end = $this->getDate($end);
	    }
		if (!$start && !$end) {
			throw new sfException('period must be setted');
		}
		if (count($this->datas) > 0) {
			$result = array();
			$csv = new ExportCsv(array('#DATE', self::OUT_VISA, self::OUT_MERCURIALE, self::OUT_CP_CODE, self::OUT_CP_LIBELLE, self::OUT_VOL, self::OUT_PRIX, self::OUT_BIO, self::OUT_VRAC_ID, self::OUT_ORDRE), "\r\n");
			foreach ($this->datas as $datas) {
				if (!preg_match('/^[0-9\-]{10}$/', $datas[self::IN_DATE])) {
					continue;
				}
				if (!$withCR && $datas[self::IN_CP_CODE] == 'CR') {
				    continue;
				}
				if ($this->mercuriale && !in_array($datas[self::IN_MERCURIAL], explode('_', $this->mercuriale))) {
				    continue;
				}
				if ($datas[self::IN_DATE] >= $start && $datas[self::IN_DATE] <= $end) {
				    $key = ($datas[self::IN_BIO])? $datas[self::IN_CP_CODE].'_BIO' : $datas[self::IN_CP_CODE];
					if (!isset($result[$key])) {
						$result[$key] = array();
					}
					$result[$key][] = array(self::OUT_VISA => $datas[self::IN_VISA], self::OUT_VOL => $datas[self::IN_VOL], self::OUT_PRIX => $datas[self::IN_PRIX], self::OUT_BIO => $datas[self::IN_BIO], self::OUT_VRAC_ID => $datas[self::IN_VRAC_ID], self::OUT_ORDRE => $datas[self::IN_ORDRE]);
					$csv->add(array($datas[self::IN_DATE], $datas[self::IN_VISA], $datas[self::IN_MERCURIAL], $datas[self::IN_CP_CODE], $datas[self::IN_CP_LIBELLE], $datas[self::IN_VOL], $datas[self::IN_PRIX], $datas[self::IN_BIO] , $datas[self::IN_VRAC_ID], $datas[self::IN_ORDRE]));
				}
			}
			if (!file_exists($this->publicPdfPath.$this->csvFilename) ||  (file_exists($this->publicPdfPath.$this->csvFilename) && $withCR)) {
			    file_put_contents($this->publicPdfPath.$this->csvFilename, $csv->output());
			}
			return $this->aggStats($result, $bio);
		}
		return array();
	}
	
	private function aggStats($datas, $bio = 0)	
	{
		$result = array();
		$c = array();
		$l = array();
		foreach ($datas as $k => $values) {
		    if ($bio === 1 && strpos($k, "_BIO") === false) {
		        continue;
		    }
		    if ($bio === 0 && strpos($k, "_BIO") !== false) {
		        continue;
		    }
		    $cep = str_replace('_BIO', '', $k);
			$nb = count($values);
			$volume = 0;
			$prix = 0;
			$min = 0;
			$max = 0;
			$contrats = array();
			$i = 0;
			foreach ($values as $val) {
                $ordre = $val[self::OUT_ORDRE];
			    $i++;
				$volume += $val[self::OUT_VOL] * 1;
				$prix += ($val[self::OUT_PRIX] * 1) * ($val[self::OUT_VOL] * 1);
				$contrats[$val[self::OUT_VISA]] = 1;
				$c[$val[self::OUT_VISA]] = 1;
				$l[$cep.'_'.$i.'_'.$val[self::OUT_VISA]] = 1;
				if (!$min ||  $val[self::OUT_PRIX] * 1 < $min) {
					$min = $val[self::OUT_PRIX] * 1;
				}
				if ($val[self::OUT_PRIX] * 1 > $max) {
					$max = $val[self::OUT_PRIX] * 1;
				}
			}
			if ($bio !== 1 && $bio !== 0 && isset($result[$ordre.$cep])) {
			     $result[$ordre.$cep][self::OUT_NB] += $nb;
			     $result[$ordre.$cep][self::OUT_CONTRAT] += count($contrats);
			     $result[$ordre.$cep][self::OUT_VOL] = number_format($volume + $result[$ordre.$cep][self::OUT_VOL], 2, '.', '');
			     $result[$ordre.$cep][self::OUT_PRIX] = number_format((($prix/$volume) + $result[$ordre.$cep][self::OUT_PRIX])/2, 2, '.', '');
			     $result[$ordre.$cep][self::OUT_MIN] = ($result[$ordre.$cep][self::OUT_MIN] < $min)? $result[$ordre.$cep][self::OUT_MIN] : number_format($min, 2, '.', '');
			     $result[$ordre.$cep][self::OUT_MAX] = ($result[$ordre.$cep][self::OUT_MAX] > $max)? $result[$ordre.$cep][self::OUT_MAX] : number_format($max, 2, '.', '');
			} else {
			    $result[$ordre.$cep] = array(self::OUT_CP_CODE => $cep, self::OUT_CP_LIBELLE => $this->getCepageLibelle($cep), self::OUT_NB => $nb, self::OUT_CONTRAT => count($contrats), self::OUT_VOL => number_format($volume, 2, '.', ''), self::OUT_PRIX => number_format($prix/$volume, 2, '.', ''), self::OUT_MIN => number_format($min, 2, '.', ''), self::OUT_MAX => number_format($max, 2, '.', ''));
			}
		}
		if ($bio === 1) {
		  $this->allContratsBio = $c;
		  $this->allLotsBio = $l;
		} else {
		  $this->allContrats = $c;
		  $this->allLots = $l;
		}
		ksort($result);
		return $result;
	}
}
