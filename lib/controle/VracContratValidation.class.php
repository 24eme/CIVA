<?php
class VracContratValidation extends DocumentValidation
{

	protected $produits_controle = array();
	
	public function __construct($document, $options = null)
    {
        parent::__construct($document, $options);
        $this->noticeVigilance = false;
    }
    
  	public function configure() 
  	{
    	$this->addControle('erreur', 'doublon_produits', 'Vous ne pouvez pas déclarer des produits identiques.');
  	}

  	public function controle()
  	{
  		$this->produits_controle = array();
		$doublon_libelles = array();
		$produits = array();
	  	foreach ($this->document->declaration->getActifProduitsDetailsSorted() as $details) {
			foreach ($details as $detail) {
				if (!isset($produits[$detail->getCepage()->getHash()])) {
					$produits[$detail->getCepage()->getHash()] = array();
				}
				$produits[$detail->getCepage()->getHash()][] = $detail;
			}
			foreach ($produits as $produit) {
				if (count($produit) > 1) {
					$produit_tmp = $produit;
					foreach ($produit as $key => $detail) {
						unset($produit_tmp[$key]);
						foreach ($produit_tmp as $d) {
							if ($detail->lieu_dit == $d->lieu_dit && $detail->vtsgn == $d->vtsgn && $detail->millesime == $d->millesime) {
								$doublon_libelles[$detail->getHash()] = $detail->getLibelle();
								$this->produits_controle[$detail->getHash()] = $detail;
							}
						}
					}
				}
			}
		}
	    if (count($doublon_libelles) > 0) {
	      $this->addPoint('erreur', 'doublon_produits', implode(",", $doublon_libelles)); 
	    }
  	}

  	public function getProduitsHashInError() {

  		return array_keys($this->produits_controle);
  	}
}