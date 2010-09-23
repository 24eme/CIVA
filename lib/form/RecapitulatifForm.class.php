<?php

    class RecapitulatifForm extends sfCouchdbFormDocumentJson {
        public function configure() {
	  $lieu = $this->getObject();
          $is_unique_acheteur = $lieu->hasSellToUniqueAcheteur();
	  foreach($lieu->acheteurs as $cvi => $acheteur) {
            if ($is_unique_acheteur) {
                $acheteur->superficie = $lieu->getTotalSuperficie();
                $acheteur->dontdplc = $lieu->getTotalDPLC();
            }
	    $af = new RecapitulatifAcheteurForm($acheteur);
	    $this->embedForm('cvi_'.$cvi, $af);
	  }
	  $this->widgetSchema->setNameFormat('recapitulatif[%s]');

	}
    }

?>