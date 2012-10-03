<?php

class DRRecolte extends BaseDRRecolte {

    public function getNoeuds() {

        return $this->getCertifications();
    }

    public function getNoeudAppellations() {

        return $this->certification->genre;
    }

    public function getAppellations() {

        return $this->getNoeudAppellations()->getAppellations();
    }


    public function getCertifications() {

        return $this->filter('^certification_');
    }

    /**
     *
     * @param array $params
     */
    protected function update($params = array()) {
        parent::update($params);

        if (in_array('from_acheteurs',$params)) {
            $acheteurs = $this->getCouchdbDocument()->getAcheteurs();
            foreach($acheteurs as $key => $appellation) {
                $app = $this->certification->genre->add($key);
                if (!$app->getConfig()->hasManyLieu()) {
                    $lieu = $app->mention->add('lieu');
                    foreach ($lieu->getConfig()->filter('^couleur') as $k => $v) {
                        $lieu->add($k);
                    }
                }
            }
            foreach($this->getAppellations() as $key => $appellation) {
                if (!$acheteurs->exist($key)) {
                    $this->remove($key);
                }
            }
        }
    }

}
