<?php
class VracMercuriale
{

	CONST IN_DATE = 0;
	CONST IN_MERCURIAL = 1;
	CONST IN_CP_CODE = 2;
	CONST IN_CP_LIBELLE = 3;
	CONST IN_VOL = 4;
	CONST IN_PRIX = 5;
	
	CONST OUT_MERCURIALE = "MERCURIALE";
	CONST OUT_CP_CODE = "CEPAGE CODE";
	CONST OUT_CP_LIBELLE = "CEPAGE LIBELLE";
	CONST OUT_VOL = "VOLUME";
	CONST OUT_PRIX = "PRIX";
	CONST OUT_MIN = "PRIX_MIN";
	CONST OUT_MAX = "PRIX_MAX";
	
	CONST NB_MIN_TO_AGG = 6;
	
	public static $cepages = array(
			'ED' => 'Edelzwicker',
			'GW' => 'Gewurztraminer',
			'MU' => 'Muscat',
			'PB' => 'Pinot Blanc',
			'PG' => 'Pinot Gris',
			'PR' => 'Pinot Noir',
			'RI' => 'Riesling',
			'SY' => 'Sylvaner / CH',
			'CR' => 'Cremant',
	);
	
	protected $datas;
	
	protected $folderPath;
	protected $start;
	protected $end;
	protected $mercuriale;
	protected $context;

	public function __construct($folderPath, $start = null, $end = null, $mercuriale = null)
	{
		$this->folderPath = $folderPath;
		$csvFile = $this->folderPath.date('Ymd', time() - 3600 * 24).'_mercuriale.csv';
		$this->generateMercurialeDatasFile($csvFile);
		$this->datas = $this->getDatasFromCsvFile($csvFile); 
		$this->start = $this->getDate($start);
		$this->end = $this->getDate($end);
		$this->mercuriale = $mercuriale;
		$this->context = null;
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
		$csv = new ExportCsv(array('#DATE', self::OUT_MERCURIALE, self::OUT_CP_CODE, self::OUT_CP_LIBELLE, self::OUT_VOL, self::OUT_PRIX), "\r\n");
		foreach ($items as $date => $values) {
			foreach ($values as $result) {
				$csv->add(array($date, $result[self::OUT_MERCURIALE], $result[self::OUT_CP_CODE], $result[self::OUT_CP_LIBELLE], number_format($result[self::OUT_VOL]*1, 2, ',', ''), number_format($result[self::OUT_PRIX]*1, 2, ',', '')));
			}
		}
		file_put_contents($csvFile, $csv->output());
	}
	
	protected function getMercurialeDatas($from = '1990-01-01', $to = null) {
		$items = array();
		$to = ($to)? $to :  date('Y-m-d', time() - 3600 * 24);
		$contrats = VracContratsView::getInstance()->findForDb2Export(array($from, $to));
		foreach($contrats as $contrat) {
			if ($date = $contrat->value[VracContratsView::VALUE_DATE_MODIF]) {
				$mercuriale = $contrat->value[VracContratsView::VALUE_MERCURIALES];
				$produits = VracProduitsView::getInstance()->findForDb2Export($contrat->value[VracContratsView::VALUE_NUMERO_ARCHIVE]);
				foreach ($produits as $produit) {
					if ($cepage = $this->getCepage($produit->value[VracProduitsView::VALUE_CEPAGE])) {
						$volume = ($produit->value[VracProduitsView::VALUE_VOLUME_ENLEVE])? $produit->value[VracProduitsView::VALUE_VOLUME_ENLEVE] : $produit->value[VracProduitsView::VALUE_VOLUME_PROPOSE];
						$prix = $produit->value[VracProduitsView::VALUE_PRIX_UNITAIRE] / 100;
						if (!isset($items[$date])) {
							$items[$date] = array();
						}
						$items[$date][] = array(self::OUT_MERCURIALE => $mercuriale, self::OUT_CP_CODE => strtoupper($cepage), self::OUT_CP_LIBELLE => strtoupper($this->getCepageLibelle($cepage)), self::OUT_VOL => $volume, self::OUT_PRIX => $prix);
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
	
	public function getStats()
	{
		if (count($this->datas) > 0) {
			if (count($this->datas[0]) != self::IN_NB_COL) {
				throw new sfException('csv format not valid');
			}
			$result = array();
			foreach ($this->datas as $datas) {
				if (!preg_match('/^[0-9]{8}$/', $datas[self::IN_DATE])) {
					continue;
				}
				if ($datas[self::IN_DATE] >= $this->start && $datas[self::IN_DATE] <= $this->end) {
					if (!isset($result[$datas[self::IN_CP]])) {
						$result[$datas[self::IN_CP]] = array();
					}
					$result[$datas[self::IN_CP]][] = array(self::OUT_VOL => $datas[self::IN_VOL], self::OUT_PRIX => $datas[self::IN_PRIX]);
				}
			}
			return $this->aggStats($result);
		}
		return array();
	}
	
	private function aggStats($datas)
	{
		$result = array();
		foreach ($datas as $cep => $values) {
			$nb = count($values);
			$volume = 0;
			$prix = 0;
			$min = 0;
			$max = 0;
			foreach ($values as $val) {
				$volume += str_replace(',', '.', $val[self::OUT_VOL]) * 1;
				$prix += str_replace(',', '.', $val[self::OUT_PRIX]) * 1;
				if (!$min ||  str_replace(',', '.', $val[self::OUT_PRIX]) * 1 < $min) {
					$min = str_replace(',', '.', $val[self::OUT_PRIX]) * 1;
				}
				if (str_replace(',', '.', $val[self::OUT_PRIX]) * 1 > $max) {
					$max = str_replace(',', '.', $val[self::OUT_PRIX]) * 1;
				}
			}
			$result[$cep] = array(self::OUT_CP => $cep, self::OUT_NB => $nb, self::OUT_VOL => number_format($volume, 2, ',', ''), self::OUT_PRIX => number_format($prix/$nb, 2, ',', ''), self::OUT_MIN => number_format($min, 2, ',', ''), self::OUT_MAX => number_format($max, 2, ',', ''));			
		}
		return $result;
	}
}