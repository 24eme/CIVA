<?php

    class RecapitulatifForm extends sfCouchdbFormDocumentJson {
        public function configure() {
	  $lieu = $this->getObject();
          $is_unique_acheteur = $lieu->hasSellToUniqueAcheteur();
	  foreach($lieu->acheteurs as $cvi => $acheteur) {
            if ($is_unique_acheteur && is_null($acheteur->superficie) && is_null($acheteur->dontdplc)) {
                $acheteur->superficie = $lieu->getTotalSuperficie();
                $acheteur->dontdplc = $lieu->getDPLCFinal();
            }
	    $af = new RecapitulatifAcheteurForm($acheteur);
	    $this->embedForm('cvi_'.$cvi, $af);
	  }

          $this->getValidatorSchema()->setPostValidator(new ValidatorRecapitulatif(null, array('object' => $this->getObject())));

	  $this->widgetSchema->setNameFormat('recapitulatif[%s]');

	}
    }

?>