<?php

class DRClient extends sfCouchdbClient {
    public function retrieveByCampagneAndCvi($cvi, $campagne) {
        return parent::retrieveDocumentById('DR-'.$cvi.'-'.$campagne);
    }

    public function getArchivesCampagnes($cvi, $campagne) {
        $docs = $this->startkey('DR-'.$cvi.'-0000')->endkey('DR-'.$cvi.'-'.$campagne)->execute();
        $campagnes = array();
        foreach($docs->getIds() as $doc_id) {
            preg_match('/DR-(?P<cvi>\d+)-(?P<campagne>\d+)/', $doc_id, $matches);
            $campagnes[$doc_id] = $matches['campagne'];
        }
        return $campagnes;
    }
}
