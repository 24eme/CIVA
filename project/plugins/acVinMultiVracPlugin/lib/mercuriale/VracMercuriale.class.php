<?php
class VracMercuriale
{

	CONST IN_DATE = 0;
	CONST IN_VISA = 1;
	CONST IN_MERCURIAL = 2;
	CONST IN_CP_CODE = 3;
	CONST IN_CP_LIBELLE = 4;
	CONST IN_VOL = 5;
	CONST IN_PRIX = 6;
	
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
	
	CONST NB_MIN_TO_AGG = 6;
	
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
	protected $pdfFilename;
	protected $start;
	protected $end;
	protected $mercuriale;
	protected $context;
	protected $allContrats;
	protected $allLots;

	public function __construct($folderPath, $start = null, $end = null, $mercuriale = null)
	{
		$this->folderPath = $folderPath;
		$csvFile = $this->folderPath.date('Ymd', time() - 3600 * 24).'_mercuriale.csv';
		$this->generateMercurialeDatasFile($csvFile);
		$this->datas = $this->getDatasFromCsvFile($csvFile); 
		$this->start = $this->getDate($start);
		$this->end = $this->getDate($end);
		$this->mercuriale = $mercuriale;
		$this->pdfFilename = $this->start.'_'.$this->end.'_mercuriales.pdf';
		$this->context = null;
		$this->allContrats = 0;
		$this->allLots = 0;
	}
	
	public function getAllContrats()
	{
	    return $this->allContrats;
	}
	
	public function getAllLots()
	{
	    return $this->allLots;
	}
	
	public function getFolderPath()
	{
	    return $this->folderPath;
	}
	
	public function getPdfFilname()
	{
	    return $this->pdfFilename;
	}
	
	public function getStart($f = 'd/m/Y')
	{
	    $dt = new DateTime(substr($this->start, 0, 4).'-'.substr($this->start, 4, 2).'-'.substr($this->start, -2));
	    return $dt->format($f);
	}
	
	public function getEnd($f = 'd/m/Y')
	{
	    $dt = new DateTime(substr($this->end, 0, 4).'-'.substr($this->end, 4, 2).'-'.substr($this->end, -2));
	    return $dt->format($f);
	}
	
	public function setContext($context) {
		$this->context = $context;
	}
	
	protected function getDate($date)
	{
		if (!$date)
			return null;
			$date = str_replace('-', '', $date);
			if (!preg_match('/^[0-9]{8}$/', $date)) {
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
		$csv = new ExportCsv(array('#DATE', self::OUT_VISA, self::OUT_MERCURIALE, self::OUT_CP_CODE, self::OUT_CP_LIBELLE, self::OUT_VOL, self::OUT_PRIX), "\r\n");
		foreach ($items as $date => $values) {
			foreach ($values as $result) {
				$csv->add(array($date, $result[self::OUT_VISA], $result[self::OUT_MERCURIALE], $result[self::OUT_CP_CODE], $result[self::OUT_CP_LIBELLE], number_format($result[self::OUT_VOL]*1, 2, ',', ''), number_format($result[self::OUT_PRIX]*1, 2, ',', '')));
			}
		}
		file_put_contents($csvFile, $csv->output());
	}
	
	protected function getMercurialeDatas($from = '1990-01-01', $to = null) {
		$items = array();
		$to = ($to)? $to :  date('Y-m-d', time() - 3600 * 24);
		$contrats = VracContratsView::getInstance()->findForDb2Export(array($from, $to));
		foreach($contrats as $contrat) {
			if ($date = $contrat->value[VracContratsView::VALUE_DATE_TRAITEMENT]) {
				$mercuriale = $contrat->value[VracContratsView::VALUE_MERCURIALES];
				$produits = VracProduitsView::getInstance()->findForDb2Export($contrat->value[VracContratsView::VALUE_NUMERO_ARCHIVE]);
				foreach ($produits as $produit) {
				    if ($produit->value[VracProduitsView::VALUE_CODE_APPELLATION] == 'GRDCRU') {
				        continue;
				    }
				    if ($produit->value[VracProduitsView::VALUE_VTSGN]) {
				        continue;
				    }
					if ($cepage = $this->getCepage($produit->value[VracProduitsView::VALUE_CEPAGE])) {
						$volume = ($produit->value[VracProduitsView::VALUE_VOLUME_ENLEVE])? $produit->value[VracProduitsView::VALUE_VOLUME_ENLEVE] : $produit->value[VracProduitsView::VALUE_VOLUME_PROPOSE];
						$prix = $produit->value[VracProduitsView::VALUE_PRIX_UNITAIRE] / 100;
						if (!isset($items[$date])) {
							$items[$date] = array();
						}
						$items[$date][] = array(self::OUT_VISA => $contrat->value[VracContratsView::VALUE_NUMERO_ARCHIVE], self::OUT_MERCURIALE => $mercuriale, self::OUT_CP_CODE => strtoupper($cepage), self::OUT_CP_LIBELLE => strtoupper($this->getCepageLibelle($cepage)), self::OUT_VOL => $volume, self::OUT_PRIX => $prix);
					}
				}
			}
		}
		ksort($items);
		return $items;
	}
	
	protected function getCepage($cepage)
	{
		if ($cepage == "BL" || $cepage == "RS") {
			$cepage = "CR";
		}
		if ($cepage == "AU" || $cepage == "PI") {
			$cepage = "PB";
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
	
	public function generateMercurialePlotFiles($cepages = array())
	{
		if (!count($cepages)) { return; }
		$csvFile = $this->folderPath.date('Ymd', time() - 3600 * 24).'_plotdatas_'.implode('_', $cepages).'.csv';
		$confFile = $this->folderPath.date('Ymd', time() - 3600 * 24).'_plot_'.implode('_', $cepages).'.conf';
		
		if (!file_exists($csvFile)) { 
			$items = $this->getMercurialePlotDatas($cepages);
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
		}
		
		file_put_contents($confFile, $this->getPlotConfig(array('csvFile' => $csvFile, 'datas' => $this->getDatasFromCsvFile($csvFile), 'cols' => $cepages, 'file' => str_replace('.conf', '.svg', $confFile))));
	    exec("gnuplot $confFile");
	}
	
	protected function getMercurialePlotDatas($cepages = array())
	{
		if (!count($this->datas)) { return array(); }
		if (!count($cepages)) { return array(); }
		$result = array();
		foreach ($this->datas as $datas) {
			if (!in_array($datas[self::IN_CP_CODE], $cepages)) {
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
			$result[$periode][$datas[self::IN_CP_CODE]][] = str_replace(',', '.', $datas[self::IN_PRIX]) * 1;
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
			foreach ($values as $cepage => $prix) {
				$nb = count($prix);
				$datas[$periode][$cepage] = ($nb >= self::NB_MIN_TO_AGG)? round(array_sum($prix)/$nb, 2) : null;
			}
		}
		return $datas;
	}
	
	protected function getPlotPeriode($date)
	{
		return (substr($date, -2) > 15)? (substr($date, 0, 6).'2')*1 : (substr($date, 0, 6).'1')*1;
	}
	
	public function getOrdre($cep)
	{
	    return (isset(self::$ordres[$this->getCepage($cep)]))? self::$ordres[$this->getCepage($cep)] : 9;
	}
	
	public function getCumul($withCR = false)
	{
	    if (!$this->start && !$this->end) {
	        throw new sfException('period must be setted');
	    }
	    $tabDate = array(substr($this->end, 0, 4), substr($this->end, 4, 2), substr($this->end, -2));

	    $currentPeriode = $this->getStats(($tabDate[0]-1).'-12-01', $this->end, $withCR);
	    $nbContratsCurrent = count($this->getAllContrats());
	    $nbLotsCurrent = count($this->getAllLots());
	    
	    $previousPeriode = $this->getStats(($tabDate[0]-2).'-12-01', ($tabDate[0]-1).'-'.$tabDate[1].'-'.$tabDate[2], $withCR);
	    $nbContratsPrevious = count($this->getAllContrats());
	    $nbLotsPrevious = count($this->getAllLots());
	    
	    $result[self::OUT_STATS] = array(
	        self::OUT_PREVIOUS => array(self::OUT_NB => $nbLotsPrevious, self::OUT_CONTRAT => $nbContratsPrevious),
	        self::OUT_CURRENT => array(self::OUT_NB => $nbLotsCurrent, self::OUT_CONTRAT => $nbContratsCurrent),
	        self::OUT_VARIATION => array(self::OUT_NB => ($nbLotsCurrent - $nbLotsPrevious), self::OUT_CONTRAT => ($nbContratsCurrent - $nbContratsPrevious))
	    );
	    
	    foreach ($currentPeriode as $cep => $datas) {
	       $ordre = $this->getOrdre($cep);
	       $result[$ordre.$cep] = array(
	           self::OUT_CP_CODE => $datas[self::OUT_CP_CODE], 
	           self::OUT_CP_LIBELLE => $datas[self::OUT_CP_LIBELLE], 
	           self::OUT_CURRENT => array(
	               self::OUT_START => ($tabDate[0]-1).'-12-01', 
	               self::OUT_END => $tabDate[0].'-'.$tabDate[1].'-'.$tabDate[2], 
	               self::OUT_NB => $datas[self::OUT_NB], 
	               self::OUT_CONTRAT => $datas[self::OUT_CONTRAT], 
	               self::OUT_VOL => $datas[self::OUT_VOL], 
	               self::OUT_PRIX => $datas[self::OUT_PRIX]), 
	           self::OUT_PREVIOUS => array(
	               self::OUT_START => ($tabDate[0]-2).'-12-01', 
	               self::OUT_END => ($tabDate[0]-1).'-'.$tabDate[1].'-'.$tabDate[2], 
	               self::OUT_NB => 0, 
	               self::OUT_CONTRAT => 0, 
	               self::OUT_VOL => number_format(0, 2, ',', ''), 
	               self::OUT_PRIX => number_format(0, 2, ',', ''))
	       );
	    }
	    foreach ($previousPeriode as $cep => $datas) {
	       $ordre = $this->getOrdre($cep);
	        if (!isset($result[$ordre.$cep])) {
	           $result[$ordre.$cep] = array(
	                self::OUT_CP_CODE => $datas[self::OUT_CP_CODE],
	                self::OUT_CP_LIBELLE => $datas[self::OUT_CP_LIBELLE],
	                self::OUT_PREVIOUS => array(
	                    self::OUT_START => ($tabDate[0]-2).'-12-01',
	                    self::OUT_END => ($tabDate[0]-1).'-'.$tabDate[1].'-'.$tabDate[2],
	                    self::OUT_NB => $datas[self::OUT_NB],
	                    self::OUT_CONTRAT => $datas[self::OUT_CONTRAT],
	                    self::OUT_VOL => $datas[self::OUT_VOL],
	                    self::OUT_PRIX => $datas[self::OUT_PRIX]),
	                self::OUT_CURRENT => array(
	                    self::OUT_START => ($tabDate[0]-1).'-12-01', 
	                    self::OUT_END => $tabDate[0].'-'.$tabDate[1].'-'.$tabDate[2], 
	                    self::OUT_NB => 0,
	                    self::OUT_CONTRAT => 0, 
	                    self::OUT_VOL => number_format(0, 2, ',', ''),
	                    self::OUT_PRIX => number_format(0, 2, ',', ''))
	            );
	        } else {
	            $result[$ordre.$cep][self::OUT_PREVIOUS][self::OUT_NB] = $datas[self::OUT_NB];
	            $result[$ordre.$cep][self::OUT_PREVIOUS][self::OUT_CONTRAT] = $datas[self::OUT_CONTRAT];
	            $result[$ordre.$cep][self::OUT_PREVIOUS][self::OUT_VOL] = $datas[self::OUT_VOL];
	            $result[$ordre.$cep][self::OUT_PREVIOUS][self::OUT_PRIX] = $datas[self::OUT_PRIX];
	        }
	        $varNb = ($result[$ordre.$cep][self::OUT_CURRENT][self::OUT_NB]) - ($result[$ordre.$cep][self::OUT_PREVIOUS][self::OUT_NB]);
	        $varContrat = ($result[$ordre.$cep][self::OUT_CURRENT][self::OUT_CONTRAT]) - ($result[$ordre.$cep][self::OUT_PREVIOUS][self::OUT_CONTRAT]);
	        $varVol = (str_replace(',', '.', $result[$ordre.$cep][self::OUT_CURRENT][self::OUT_VOL]) * 1) - (str_replace(',', '.', $result[$ordre.$cep][self::OUT_PREVIOUS][self::OUT_VOL]) * 1);
	        $varPrix = (str_replace(',', '.', $result[$ordre.$cep][self::OUT_CURRENT][self::OUT_PRIX]) * 1) - (str_replace(',', '.', $result[$ordre.$cep][self::OUT_PREVIOUS][self::OUT_PRIX]) * 1);
	        $varVolPerc = round(($varVol * 100) / (str_replace(',', '.', $result[$ordre.$cep][self::OUT_PREVIOUS][self::OUT_VOL]) * 1));
	        $varPrixPerc = round(($varPrix * 100) / (str_replace(',', '.', $result[$ordre.$cep][self::OUT_PREVIOUS][self::OUT_PRIX]) * 1));
	        
	        $result[$ordre.$cep][self::OUT_VARIATION] = array(
	            self::OUT_NB => $varNb,
	            self::OUT_CONTRAT => $varContrat,
	            self::OUT_VOL => number_format($varVol, 2, ',', ''),
	            self::OUT_PRIX => number_format($varPrix, 2, ',', ''),
	            self::OUT_VOL_PERC => ($varVolPerc)? $varVolPerc : 0,
	            self::OUT_PRIX_PERC => ($varPrixPerc)? $varPrixPerc : 0,
	        );
	    }
	    ksort($result);
	    return $result;
	}
	
	public function getStats($start = null, $end = null, $withCR = false)
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
			foreach ($this->datas as $datas) {
				if (!preg_match('/^[0-9]{8}$/', $datas[self::IN_DATE])) {
					continue;
				}
				if (!$withCR && $datas[self::IN_CP_CODE] == 'CR') {
				    continue;
				}
				if ($datas[self::IN_DATE] >= $start && $datas[self::IN_DATE] <= $end) {
					if (!isset($result[$datas[self::IN_CP_CODE]])) {
						$result[$datas[self::IN_CP_CODE]] = array();
					}
					$result[$datas[self::IN_CP_CODE]][] = array(self::OUT_VISA => $datas[self::IN_VISA], self::OUT_VOL => $datas[self::IN_VOL], self::OUT_PRIX => $datas[self::IN_PRIX]);
				}
			}
			return $this->aggStats($result);
		}
		return array();
	}
	
	private function aggStats($datas)
	{
		$result = array();
		$c = array();
		$l = array();
		foreach ($datas as $cep => $values) {
		    $ordre = $this->getOrdre($cep);
			$nb = count($values);
			$volume = 0;
			$prix = 0;
			$min = 0;
			$max = 0;
			$contrats = array();
			$i = 0;
			foreach ($values as $val) {
			    $i++;
				$volume += str_replace(',', '.', $val[self::OUT_VOL]) * 1;
				$prix += (str_replace(',', '.', $val[self::OUT_PRIX]) * 1) * (str_replace(',', '.', $val[self::OUT_VOL]) * 1);
				$contrats[$val[self::OUT_VISA]] = 1;
				$c[$val[self::OUT_VISA]] = 1;
				$l[$cep.'_'.$i.'_'.$val[self::OUT_VISA]] = 1;
				if (!$min ||  str_replace(',', '.', $val[self::OUT_PRIX]) * 1 < $min) {
					$min = str_replace(',', '.', $val[self::OUT_PRIX]) * 1;
				}
				if (str_replace(',', '.', $val[self::OUT_PRIX]) * 1 > $max) {
					$max = str_replace(',', '.', $val[self::OUT_PRIX]) * 1;
				}
			}
			$result[$ordre.$cep] = array(self::OUT_CP_CODE => $cep, self::OUT_CP_LIBELLE => $this->getCepageLibelle($cep), self::OUT_NB => $nb, self::OUT_CONTRAT => count($contrats), self::OUT_VOL => number_format($volume, 2, ',', ''), self::OUT_PRIX => number_format($prix/$volume, 2, ',', ''), self::OUT_MIN => number_format($min, 2, ',', ''), self::OUT_MAX => number_format($max, 2, ',', ''));			
		}
		$this->allContrats = $c;
		$this->allLots = $l;
		ksort($result);
		return $result;
	}
}