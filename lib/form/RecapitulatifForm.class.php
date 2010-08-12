<?php

    class RecapitulatifForm extends sfCouchdbFormDocumentJson {
        public function configure() {
	  $lieu = $this->getObject();
	  foreach($lieu->acheteurs as $cvi => $acheteur) {
	    $af = new RecapitulatifAcheteurForm($acheteur);
	    $this->embedForm('cvi_'.$cvi, $af);
	  }
	  $this->widgetSchema->setNameFormat('recapitulatif[%s]');

	}
    }

?>