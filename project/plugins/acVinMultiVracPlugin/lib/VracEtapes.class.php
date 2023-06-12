<?php
class VracEtapes
{
	private static $_instance = null;
	protected $etapes;

	const ETAPE_SOUSSIGNES = 'soussignes';
	const ETAPE_PRODUITS = 'produits';
	const ETAPE_CONDITIONS = 'conditions';
	const ETAPE_ANNEXES = 'annexes';
	const ETAPE_VALIDATION = 'validation';

	static $libelles = array(
		self::ETAPE_SOUSSIGNES => 'Soussignés',
		self::ETAPE_PRODUITS => 'Produits',
		self::ETAPE_CONDITIONS => 'Conditions',
		self::ETAPE_ANNEXES => 'Annexes',
		self::ETAPE_VALIDATION => 'Validation'
	);

	public static function getInstance()
    {
       	if(is_null(self::$_instance)) {
       		self::$_instance = new self();
		}
		return self::$_instance;
    }
	
	public function __construct()
	{
		$config = VracClient::getConfig();
		$this->etapes = $config[VracClient::APP_CONFIGURATION_ETAPES];
	}
	
	public function getEtapes()
	{
		return $this->etapes;
	}
	
	public function getPosition($etape)
	{
		$etapes = $this->getEtapes();
		return $etapes[$etape];
	}
	
	public function getNext($etape)
	{
		$etapes = $this->getEtapes();
		$next = null;
		$postion = $etapes[$etape];
		if ($postion < count($etapes)) {
			foreach ($etapes as $et => $pos) {
				if ($pos == ($postion + 1)) {
					$next = $et;
					break;
				}
			}
		}
		return $next;
	}
	
	public function getPrev($etape)
	{
		$etapes = $this->getEtapes();
		$prev = null;
		$postion = $etapes[$etape];
		if (($postion - 1) > 0) {
			foreach ($etapes as $et => $pos) {
				if ($pos == ($postion - 1)) {
					$prev = $et;
					break;
				}
			}
		}
		return $prev;
	}
	
	public function getFirst()
	{
		$etapes = array_keys($this->getEtapes());
		return ($this->getNbEtape() > 0)? $etapes[0] : null;
	}
	
	public function getLast()
	{
		$etapes = array_keys($this->getEtapes());
		return ($this->getNbEtape() - 1 >= 0)? $etapes[$this->getNbEtape() - 1] : null;
	}
	
	public function isGt($etape1, $etape2)
	{
		$etapes = $this->getEtapes();
		return ($etapes[$etape1] > $etapes[$etape2]);
	}
	
	public function isLt($etape1, $etape2)
	{
		$etapes = $this->getEtapes();
		return ($etapes[$etape1] < $etapes[$etape2]);
	}
	
	public function getNbEtape()
	{
		return count($this->getEtapes());
	}
 	
  	public function getLibelles() 
  	{
  		return self::$libelles;
  	}
  	
  	public function getLibelle($etape)
  	{
  		$libelles = $this->getLibelles();
  		return $libelles[$etape];
  	}
  	
  	public function exist($etape)
  	{
  		return array_key_exists($etape, $this->getLibelles());
  	}
}