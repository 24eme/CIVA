<?php
class VracValidation extends DocumentValidation
{
	const MIN_VOLUME_CONTROLE = 5;
	const TAUX_VOLUME_CONTROLE = 0.1;

	protected $produits_controle = array();

	public function __construct($document, $options = null)
    {
        parent::__construct($document, $options);
        $this->noticeVigilance = false;
    }

  	public function configure()
  	{
    	$this->addControle('vigilance', 'ecart_volume', 'Il y a un écart de volume important entre le volume estimé et le volume réel d\'enlèvement.');
    	$this->addControle('erreur', 'volume_non_saisi', 'La cloture d\'un produit n\'est pas possible sans spécifier un volume réel d\'enlèvement.');
        $this->addControle('vigilance', 'volume_bloque', 'Ce contrat contient des produits dont une partie du volume est en réserve');
  	}

  	public function controle()
  	{
  		$this->produits_controle = array();
		$null_libelles = array();
		$ecart_libelles = array();
	  	foreach ($this->document->declaration->getActifProduitsDetailsSorted() as $details) {
			foreach ($details as $detail) {
				if ($detail->cloture && $detail->volume_enleve === null) {
					$null_libelles[] = $detail->getLibelle();
					$this->produits_controle[$detail->getHash()] = $detail;
				}
				if ($detail->volume_propose > self::MIN_VOLUME_CONTROLE && $detail->volume_enleve !== null) {
					$ecart = $detail->volume_propose * self::TAUX_VOLUME_CONTROLE;
					$min = $detail->volume_propose - $ecart;
					$max = $detail->volume_propose + $ecart;
					if (($detail->volume_enleve < $min || $detail->volume_enleve > $max) && $detail->cloture) {
						$ecart_libelles[] = $detail->getLibelle();
						$this->produits_controle[$detail->getHash()] = $detail;
					}
				}
			}
		}

	    if ($this->document->needRetiraison() && count($null_libelles) > 0) {
	      $this->addPoint('erreur', 'volume_non_saisi', implode(",", $null_libelles));
	    }

	    if(count($ecart_libelles) > 0){
	      $this->addPoint('vigilance', 'ecart_volume', implode(",", $ecart_libelles));
	    }

        if($produitsVolumeBloque = $this->document->declaration->getProduitsWithVolumeBloque()) {
            $produits = array_map(function($val) { return $val->getLibelleComplet(); }, $produitsVolumeBloque);
            $this->addPoint('vigilance', 'volume_bloque', implode(", ", $produits), $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'produits')));
        }
  	}

  	public function getProduitsHashInError() {

  		return array_keys($this->produits_controle);
  	}
}
