<?php
/**
 * Model for SVApporteurs
 *
 */

class SVApporteurs extends BaseSVApporteurs {
    public function reorderByConf() {
        $children = array();

        foreach($this as $hash => $child) {
            $children[$hash] = $child->getData();
        }

        foreach($children as $hash => $child) {
            $this->remove($hash);
        }

        foreach($this->getConfig()->getProduits() as $hash => $child) {
            $hashProduit = str_replace("/declaration/", "", $hash);
            if(!array_key_exists($hashProduit, $children)) {
                continue;
            }
            $this->add($hashProduit, $children[$hashProduit]);
        }
    }
}