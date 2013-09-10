<?php
class VracTousView extends acCouchdbView
{

    public static function getInstance() {

        return acCouchdbManager::getView('VRAC', 'tous', 'Vrac');
    }

    public function findByIdentifiant($identifiant) {    

        return $this->client->startkey(array($identifiant))
                            ->endkey(array($identifiant, array()))
                            ->getView($this->design, $this->view)->rows;
    }
}  