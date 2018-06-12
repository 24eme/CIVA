<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class mercurialeVracTask
 */
class mercurialeVracTask extends sfBaseTask
{
	CONST CP = "CEPAGE";
	CONST NB = "NB_LOTS";
	CONST VOL = "VOLUME";
	CONST PRIX = "PRIX_MOYEN";
	CONST MIN = "PRIX_MIN";
	CONST MAX = "PRIX_MAX";
	protected static $cepages = array(
			'BN' => 'Pinot Noir Blanc',
			'CD' => 'Chardonnay',
			'CH' => 'Chasselat',
			'CR' => 'Cremant',
			'ED' => 'Edelzwicker',
			'GW' => 'Gewurztraminer',
			'KL' => 'Klevener',
			'MU' => 'Muscat',
			'PB' => 'Pinot Blanc',
			'PG' => 'Pinot Gris',
			'PN' => 'Pinot Noir Rose',
			'PR' => 'Pinot Noir',
			'RI' => 'Riesling',
			'SY' => 'Sylvaner',
	);
	
    protected function configure()
    {
        $this->addArguments(array(
            new sfCommandArgument('folderPath', sfCommandArgument::REQUIRED, 'folderPath'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'mercuriale';
        $this->name = 'vrac';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [mercurialeVrac|INFO] task does things.
Call it with:

  [php symfony mercuriale:vrac|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        set_time_limit(0);
        
        $folderPath = $arguments['folderPath'];
        
        $items = array();
        $produits = VracProduitsView::getInstance()->getAll();
        foreach ($produits as $produit) {
        	if($produit->value[VracProduitsView::VALUE_CODE_APPELLATION] == "VINTABLE") {
        		continue;
        	}
        	if (!preg_match('/^[0-9]{8}$/', $produit->value[VracProduitsView::VALUE_DATE_CIRCULATION])) {
        		continue;
        	}
        	
        	$cepage = $this->getCepage($produit->value[VracProduitsView::VALUE_CEPAGE]);
        	$volume = $produit->value[VracProduitsView::VALUE_VOLUME_ENLEVE];
        	$prix = $produit->value[VracProduitsView::VALUE_PRIX_UNITAIRE] / 100;
        	$aaaamm = substr($produit->value[VracProduitsView::VALUE_DATE_CIRCULATION], 0, 6);
        	$semaine = (substr($produit->value[VracProduitsView::VALUE_DATE_CIRCULATION], -2) > 15)? 1 : 0;
        	
        	if (!isset($items[$aaaamm]))
        		$items[$aaaamm] = array();
        	if (!isset($items[$aaaamm][$semaine]))
        		$items[$aaaamm][$semaine] = array();
        	if (!isset($items[$aaaamm][$semaine][$cepage]))
        		$items[$aaaamm][$semaine][$cepage] = array();
        	
        	$items[$aaaamm][$semaine][$cepage][] = array(self::VOL => $volume, self::PRIX => $prix);
        }
        ksort($items);
        $mercuriale = array();
        $allMercuriale = '';
        foreach ($items as $periode => $value) {
        	ksort($value);
        	foreach ($value as $semaine => $result) {
        		$path = $folderPath.'/'.$periode.'_'.$semaine.'.csv';
        		$jour = ($semaine > 0)? '15' : '01';
        		$csv = $this->getCsvStats($result, substr($periode, 0,4).'-'.substr($periode, -2).'-'.$jour);
        		$output = $csv->output();
        		file_put_contents($path, $output);
        		$allMercuriale .= ($allMercuriale)? substr($output, strpos($output, "\r\n") + strlen("\r\n")) :  $output;
        	}
        }
        file_put_contents($folderPath.'/'.date('Ymd').'_mercuriale.csv', $allMercuriale);
        echo sprintf("Les mercuriales des transactions vrac Alsace AOC ont été générées dans %s\n", $folderPath);
    }

    protected function getCsvStats($values, $date)
    {
    	$csv = new ExportCsv(array('#DATE', self::CP, self::NB, self::VOL, self::PRIX, self::MIN, self::MAX), "\r\n");
    	foreach ($values as $cepage => $items) {
	    	$nb = count($items);
	    	$volume = 0;
	    	$prix = 0;
	    	$min = 0;
	    	$max = 0;
	    	foreach ($items as $val) {
	    		$volume += $val[self::VOL];
	    		$prix += $val[self::PRIX];
	    		if (!$min ||  $val[self::PRIX] < $min) {
	    			$min = $val[self::PRIX];
	    		}
	    		if ($val[self::PRIX] > $max) {
	    			$max = $val[self::PRIX];
	    		}
	    	}
	    	$csv->add(array($date, self::CP => strtoupper($this->getCepageLibelle($cepage)), self::NB => $nb, self::VOL => number_format($volume, 2, ',', ''), self::PRIX => number_format($prix/$nb, 2, ',', ''), self::MIN => number_format($min, 2, ',', ''), self::MAX => number_format($max, 2, ',', '')));
    	}
    	return $csv;
    }
    
	protected function getCepageLibelle($cepage)
	{
		return (isset(self::$cepages[$cepage]))? self::$cepages[$cepage] : $cepage;
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
        
    	return $cepage;
    }
}
