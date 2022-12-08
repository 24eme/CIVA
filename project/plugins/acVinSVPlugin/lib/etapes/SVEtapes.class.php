<?php
class SVEtapes extends Etapes
{
	const ETAPE_APPORTEURS = 'apporteurs';
	const ETAPE_AUTRES = 'autres';
    const ETAPE_LIEU_STOCKAGE = 'stockage';
	const ETAPE_VALIDATION = 'validation';

	public static $etapes = array(
        self::ETAPE_APPORTEURS => 0,
        self::ETAPE_AUTRES => 1,
        self::ETAPE_LIEU_STOCKAGE => 2,
        self::ETAPE_VALIDATION => 3
    );

	public static $links = array(
        self::ETAPE_APPORTEURS => 'sv_apporteurs',
        self::ETAPE_AUTRES => 'sv_autres',
        self::ETAPE_LIEU_STOCKAGE => 'sv_stockage',
        self::ETAPE_VALIDATION => 'sv_validation'
    );

	public static $libelles = array(
        self::ETAPE_APPORTEURS => 'Apporteurs',
        self::ETAPE_AUTRES => 'Autres',
        self::ETAPE_LIEU_STOCKAGE => 'Lieux de stockage',
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

    public function isEtapeDisabled($etape, $doc)
    {
    }
}
