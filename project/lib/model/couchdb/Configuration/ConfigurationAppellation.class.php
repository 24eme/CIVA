<?php

class ConfigurationAppellation extends BaseConfigurationAppellation {

    public function getMentions() {
        return $this->filter('^mention');
    }

    public function getLieux() {

        return $this->getChildrenNodeDeep();
    }

    public function getChildrenNode() {

        return $this->getMentions();
    }

    public function hasManyLieu() {

        return $this->getChildrenNodeDeep()->hasManyNoeuds();
    }

    public function hasLieuEditable() {
        if ($this->exist('detail_lieu_editable') && $this->get('detail_lieu_editable'))
            return true;
        return false;
    }

    public function getNbMention() {
        return count($this->getMentions());
    }

    public function hasManyMention(){
        return ($this->filter('^mention')->count() > 1);
    }


/*
    public function getMention() {
        if( count($this->filter('^mention')) > 1)
        throw new sfException("getMention() ne peut être appelé d'une appellation qui a plusieurs mentions...");

        return $this->_get('mention');
    }
*/
    public function getDistinctLieux()
    {
        $arrLieux = array();
        foreach($this->getMentions() as $mention){
            foreach( $mention->getLieux() as $key =>  $lieu){
                if(!array_key_exists($key, $arrLieux)){
                    $arrLieux[$key] = $lieu;
                }
            }
        break;
        }
    return $arrLieux;
    }

}
