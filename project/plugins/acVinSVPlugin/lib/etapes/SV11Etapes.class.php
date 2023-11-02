<?php

class SV11Etapes extends SVEtapes
{
    protected function filterItems($items) {
        unset($items[self::ETAPE_EXTRACTION]);
        unset($items[self::ETAPE_REVENDICATION]);

        return $items;
	}
}
