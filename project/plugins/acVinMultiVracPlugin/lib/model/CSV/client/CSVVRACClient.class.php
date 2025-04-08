<?php

class CSVVRACClient extends acCouchdbClient
{
    const TYPE_VRAC = "VRAC";
    const LEVEL_WARNING = 'WARNING';
    const LEVEL_ERROR = 'ERROR';

    public static function getInstance()
    {
        return acCouchdbManager::getClient("CSVVRAC");
    }

    public function findFromIdentifiant($identifiant, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT)
    {
        return $this->find($this->buildId($identifiant), $hydrate);
    }

    public function createOrFind($path, DateTimeInterface $date)
    {
        $csvVrac = $this->findFromIdentifiant($date->format('YmdHis'));

        if ($csvVrac instanceof CSVVRAC) {
            $csvVrac->storeAttachment($path, 'text/csv', $csvVrac->getFileName());
            return $csvVrac;
        }

        $csvVrac = new CSVVRAC();
        $csvVrac->_id = $this->buildId($date->format('YmdHis'));
        $csvVrac->identifiant = $date->format('YmdHis');
        $csvVrac->storeAttachment($path, 'text/csv', $csvVrac->getFileName());
        $csvVrac->save();
        return $csvVrac;
    }

    public function buildId($identifiant)
    {
        return "CSVVRAC-" . $identifiant;
    }
}
