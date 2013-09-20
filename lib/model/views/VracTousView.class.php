<?php
class VracTousView extends acCouchdbView
{

    public static function getInstance() {

        return acCouchdbManager::getView('VRAC', 'tous', 'Vrac');
    }

    public function findBy($identifiant, $campagne) {    

        return $this->client->startkey(array($identifiant, $campagne))
                            ->endkey(array($identifiant, $campagne, array()))
                            ->getView($this->design, $this->view)->rows;
    }
}  