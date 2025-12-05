<?php

class CSVVRACClient extends acCouchdbClient
{
    const TYPE_VRAC = "VRAC";
    const LEVEL_WARNING = 'WARNING';
    const LEVEL_ERROR = 'ERROR';
    const LEVEL_IMPORTE = 'IMPORTE';

    public static function getInstance()
    {
        return acCouchdbManager::getClient("CSVVRAC");
    }

    public function findByIdentifiant($identifiant, $date = null, $hydrade = acCouchdbClient::HYDRATE_DOCUMENT)
    {
        $start = "00000000";
        $end = "99999999";
        if ($date) {
            $start = $end = $date;
        }
        $csvs = $this->endkey("CSVVRAC-".$identifiant."-{$start}000")
                     ->startkey("CSVVRAC-".$identifiant."-{$end}999")
                     ->descending(true)
                     ->execute();

        return $csvs;
    }

    public function createNouveau($path, Compte $compte)
    {
        $csvVrac = new CSVVRAC();
        $csvVrac->_id = $this->buildId($compte->identifiant);
        $csvVrac->identifiant = $compte->identifiant;
        $csvVrac->storeAttachment($path, 'text/csv', $csvVrac->getFileName());
        $csvVrac->save();
        return $csvVrac;
    }

    public function buildId($identifiant)
    {
        $date = (new DateTime())->format('Ymd');
        $count = count($this->findByIdentifiant($identifiant, $date)) + 1;
        return "CSVVRAC-" . $identifiant . "-" . $date . sprintf("%03d", $count);
    }
}
