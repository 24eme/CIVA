<?php
class Cepappctr
{
	protected $confFile;
	protected $conf;
	
	const CODE_APPELLATION = 0;
	const CODE_CEPAGE = 1;
	const CODE_LIBELLE = 2;
	const ORDRE_MERCURIALE = 3;
	
	public function __construct()
	{
		$this->confFile = sfConfig::get('sf_data_dir').'/configuration/CEPAPPCTR';
		$this->conf = array();
		$this->loadConf();
	}
	
	protected function loadConf()
	{
		if (($handle = fopen($this->confFile, "r")) !== FALSE) {
	    	while (($datas = fgetcsv($handle, 1000, ",")) !== FALSE) {
		        $this->conf[$datas[self::CODE_APPELLATION]][$datas[self::CODE_CEPAGE]] = $datas[self::ORDRE_MERCURIALE];
	    	}
	    	fclose($handle);
		} else {
			throw new sfException("Fichier \"".$this->confFile."\" inexistant ou illisible.");
		}
	}
	
	public function getConf()
	{
		return $this->conf;
	}
	
	public function getOrdreMercurialeByPair($codeAppellation, $codeCepage)
	{
		$result = null;
		if (isset($this->conf[$codeAppellation])) {
			if (isset($this->conf[$codeAppellation][$codeCepage])) {
				return $this->conf[$codeAppellation][$codeCepage];
			}
		}
	}
}