<?php

class SV11Etapes extends SVEtapes
{
    protected function filterItems($items) {
        unset($items[self::ETAPE_EXTRACTION]);
        unset($items[self::ETAPE_REVENDICATION]);

        self::$etapes = $items;
        $i = 0;
        foreach (self::$etapes as &$etape) {
            $etape = $i;
            $i++;
        }

        return $items;
	}
}
