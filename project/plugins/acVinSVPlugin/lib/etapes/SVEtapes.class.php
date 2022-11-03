<?php
class SVEtapes extends Etapes
{
	const ETAPE_EXPLOITATION = 'exploitation';
	const ETAPE_EXTRACTION = 'extraction';
	const ETAPE_APPORTEURS = 'apporteurs';
	const ETAPE_SAISIE = 'saisie';
	const ETAPE_AUTRES = 'autres';
	const ETAPE_VALIDATION = 'validation';

	public static $etapes = array(
        self::ETAPE_EXPLOITATION => 0,
        self::ETAPE_EXTRACTION => 1,
        self::ETAPE_APPORTEURS => 2,
        self::ETAPE_SAISIE => 3,
        self::ETAPE_AUTRES => 4,
        self::ETAPE_VALIDATION => 5
    );

	public static $links = array(
        self::ETAPE_EXPLOITATION => 'sv_exploitation',
        self::ETAPE_EXTRACTION => 'sv_extraction',
        self::ETAPE_APPORTEURS => 'sv_apporteurs',
        self::ETAPE_SAISIE => 'sv_saisie',
        self::ETAPE_AUTRES => 'sv_autres',
        self::ETAPE_VALIDATION => 'sv_validation'
    );

	public static $libelles = array(
        self::ETAPE_EXPLOITATION => 'Exploitation',
        self::ETAPE_EXTRACTION => 'Extraction',
        self::ETAPE_APPORTEURS => 'Apporteurs',
        self::ETAPE_SAISIE => 'Saisie',
        self::ETAPE_AUTRES => 'Autres',
        self::ETAPE_VALIDATION => 'Validation'
    );

	private static $_instance = null;

	public static function getInstance()
	{
		if(is_null(self::$_instance)) {
			self::$_instance = new SVEtapes();
		}
		return self::$_instance;
	}

	protected function filterItems($items) {
        return $items;
	}

    public function getEtapesHash()
    {

        return $this->filterItems(self::$etapes);
    }

    public function getRouteLinksHash()
    {

		return $this->filterItems(self::$links);
    }

    public function getLibellesHash()
    {

		return $this->filterItems(self::$libelles);
    }

}
