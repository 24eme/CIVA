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

    public function createOrFindFromVrac($path, Vrac $vrac)
    {
        $csvId = $this->buildId($vrac->numero_contrat);
        $csvVrac = $this->find($csvId);

        if ($csvVrac instanceof CSVVRAC) {
            $csvVrac->storeAttachment($path, 'text/csv', $csvVrac->getFileName());
            return $csvVrac;
        }

        $csvVrac = new CSVVRAC();
        $csvVrac->_id = $csvId;
        $csvVrac->identifiant = $vrac->numero_contrat;
        $csvVrac->storeAttachment($path, 'text/csv', $csvVrac->getFileName());
        $csvVrac->save();
        return $csvVrac;
    }

    public function buildId($identifiant)
    {
        return "CSVVRAC-" . $identifiant;
    }
}
