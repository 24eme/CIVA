<?php

    class RecapitulatifForm extends sfCouchdbFormDocumentJson {
        public function configure() {
	  $lieu = $this->getObject();
          //$is_unique_acheteur = $lieu->hasSellToUniqueAcheteur();
	  foreach($lieu->acheteurs as $type => $acheteurs_type) {
            foreach($acheteurs_type as $cvi => $acheteur) {
                /*if ($is_unique_acheteur) {
                    $acheteur->superficie = $lieu->getTotalSuperficie();
                }
                if ($is_unique_acheteur) {
                    $acheteur->dontdplc = $lieu->getDplc();
                }*/
                $af = new RecapitulatifAcheteurForm($acheteur);
                $this->embedForm($type.'_cvi_'.$cvi, $af);
            }
	  }

          $this->getValidatorSchema()->setPostValidator(new ValidatorRecapitulatif(null, array('object' => $this->getObject())));

	  $this->widgetSchema->setNameFormat('recapitulatif[%s]');

	}
    }

?>