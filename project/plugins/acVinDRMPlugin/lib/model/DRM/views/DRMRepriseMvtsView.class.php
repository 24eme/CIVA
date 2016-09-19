<?php

class DRMRepriseMvtsView extends acCouchdbView
{
    const KEY_ETABLISSEMENT = 0;
    const KEY_PERIODE = 1;
    const KEY_TYPE_DOC = 2;
    const KEY_ID_DOC = 3;

    const VALUE_PRODUIT_HASH = 0;
    const VALUE_CAT_MVT = 1;
    const VALUE_TYPE_MVT = 2;
    const VALUE_VOLUME = 3;

    public static function getInstance() {

        return acCouchdbManager::getView('drm', 'repriseMvts');
    }

    public function getRepriseMvts($etablissementId, $periode) {
      $result = $this->client->startkey(array($etablissementId, $periode))
      ->endkey(array($etablissementId, $periode, array()))
      ->getView($this->design, $this->view)->rows;

        return $result;
    }
}
