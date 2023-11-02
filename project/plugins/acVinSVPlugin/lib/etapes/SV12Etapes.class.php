<?php

class SV12Etapes extends SVEtapes
{
    public function isEtapeDisabled($etape, $doc)
    {
        if($doc->isFromCSV() && $etape == self::ETAPE_EXTRACTION) {
            return true;
        }
    }
}
