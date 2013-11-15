<?php
class VracValidation extends DocumentValidation
{
	const MIN_VOLUME_CONTROLE = 5;
	const TAUX_VOLUME_CONTROLE = 0.1;
	
	public function __construct($document, $options = null)
    {
        parent::__construct($document, $options);
        $this->noticeVigilance = false;
    }
    
  	public function configure() 
  	{
    	$this->addControle('vigilance', 'ecart_volume', 'Il y a un écart de volume important entre le volume estimé et le volume réel d\'enlèvement.');
    	$this->addControle('erreur', 'volume_non_saisi', 'La cloture d\'un produit n\'est pas possible sans spécifier un volume réel d\'enlèvement.');
  	}

  	public function controle()
  	{
		$nbnull = 0;
		$nbecart = 0;
	  	foreach ($this->document->declaration->getActifProduitsDetailsSorted() as $details) {
			foreach ($details as $detail) {
				if ($detail->cloture && !$detail->volume_enleve) {
					$nbnull++;
				}
				if ($detail->volume_propose > self::MIN_VOLUME_CONTROLE && $detail->volume_enleve > 0) {
					$ecart = $detail->volume_propose * self::TAUX_VOLUME_CONTROLE;
					$min = $detail->volume_propose - $ecart;
					$max = $detail->volume_propose + $ecart;
					if (($detail->volume_enleve < $min || $detail->volume_enleve > $max) && $detail->cloture) {
						$nbecart++;
					}
				}
			}
		}
	
	    if ($nbnull > 0) {
	      $this->addPoint('erreur', 'volume_non_saisi', $nbnull.' produit(s) concerné(s)'); 
	    }
	
	    if($nbecart > 0){
	      $this->addPoint('vigilance', 'ecart_volume', $nbecart.' produit(s) concerné(s)'); 
	    }
  	}
}