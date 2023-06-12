<?php
/**
 * Model for VracValide
 *
 */

class VracValide extends BaseVracValide {
    public function setStatut($statut, $auteur = null) {

        return $this->getDocument()->setStatut($statut, $auteur);
    }
}
