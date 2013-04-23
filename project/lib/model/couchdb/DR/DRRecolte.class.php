<?php

class DRRecolte extends BaseDRRecolte {

    public function getChildrenNode() {

        return $this->getCertifications();
    }

    public function getNoeudAppellations() {

        return $this->add('certification')->add('genre');
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
            foreach($acheteurs->getNoeudAppellations() as $key => $appellation) {
                $app = $this->getNoeudAppellations()->add($key);
                if (!$app->getConfig()->hasManyLieu()) {
                    $lieu = $app->mention->add('lieu');
                    foreach ($lieu->getConfig()->filter('^couleur') as $k => $v) {
                        $lieu->add($k);
                    }
                }
            }
            foreach($this->getAppellations() as $key => $appellation) {
                if (!$acheteurs->getNoeudAppellations()->exist($key)) {
                    $this->getNoeudAppellations()->remove($key);
                }
            }
        }
    }

}
