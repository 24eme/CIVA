<?php
class DSNoeudRouteCiva extends DSRoute implements InterfaceTiersRoute {

    protected $noeud = null;

    protected function getObjectForParameters($parameters) {
        parent::getObjectForParameters($parameters);

        $hash = $parameters['hash'];

        if(!$hash) {
            $this->noeud = null;

            return $this->ds;
        }

        if(preg_match('/^([A-Za-z0-9]+)[-]*([A-Za-z0-9]*)$/', $hash, $matches)){
            $hash_noeud = sprintf('appellation_%s/mention/lieu%s', $matches[1], $matches[2]);
            if($this->ds->declaration->getAppellations()->exist($hash_noeud)) {
                $this->noeud = $this->ds->declaration->getAppellations()->get($hash_noeud);
            } elseif($this->ds->declaration->certification->exist('genre'.$hash)) {
                $this->noeud = $this->ds->declaration->certification->get('genre'.$hash);
            }
        }

        if(!$this->noeud && preg_match('/^([A-Z]+)$/', $hash, $matches)){
            $this->noeud = $this->ds->declaration->getAppellations()->get('appellation_'.$matches[1]);
        }

        if(!$this->noeud) {

            throw new InvalidArgumentException(sprintf('The hash "%s" does not exist.', $parameters['hash']));
        }

        return $this->noeud;
    }

    protected function doConvertObjectToArray($object) {
        if($object instanceof sfOutputEscaperIteratorDecorator) {
            $object = $object->getRawValue();
        }

        $hash = null;

        if($object instanceof DSGenre) {
            $hash = str_replace("genre" , "", $object->getKey());
        }

        if($object instanceof DSAppellation) {
            $hash = str_replace("appellation_" , "", $object->getKey());
        }

        if($object instanceof DSLieu) {
            $hash = preg_replace('/-$/', '', sprintf("%s-%s", str_replace("appellation_" , "", $object->getAppellation()->getKey()), str_replace("lieu" , "", $object->getKey())));
        }

        $parameters = array("id" => $object->getDocument()->_id, "hash" => $hash);
        return $parameters;
    }

    public function getNoeud() {
        $this->getDS();

        return $this->noeud;
    }
}