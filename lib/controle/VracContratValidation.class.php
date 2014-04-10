<?php
class VracContratValidation extends DocumentValidation
{
    const PRIX_SEUIL = 50;
    const MAX_PRODUIT = 9;

	protected $produits_controle = array();
	
	public function __construct($document, $options = null)
    {
        parent::__construct($document, $options);
        $this->noticeVigilance = false;
    }
    
  	public function configure() 
  	{
        $this->addControle('erreur', 'nb_produits', 'Vous ne pouvez pas saisir plus de '.self::MAX_PRODUIT.' produits par contrat.');
  		$this->addControle('erreur', 'doublon_produits', 'Vous ne pouvez pas déclarer des produits identiques.');
    	$this->addControle('vigilance', 'prix_litre', 'Le prix doit être exprimé en €/HL et non en €/L');
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
                if((!$detail->exist('nb_bouteille') || !$detail->nb_bouteille) && $detail->prix_unitaire < self::PRIX_SEUIL) {
                    $this->addPoint('vigilance', 'prix_litre', $detail->getLibelle(), $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'produits'))); 
                }
			}
			foreach ($produits as $produit) {
				if (count($produit) > 1) {
					$produit_tmp = $produit;
					foreach ($produit as $key => $detail) {
						unset($produit_tmp[$key]);
						foreach ($produit_tmp as $d) {
							if ($detail->lieu_dit == $d->lieu_dit && $detail->vtsgn == $d->vtsgn && $detail->millesime == $d->millesime && $detail->denomination == $d->denomination) {
								$doublon_libelles[$detail->getHash()] = $detail->getLibelle();
								$this->produits_controle[$detail->getHash()] = $detail;
							}
						}
					}
				}
			}
		}
	    if (count($doublon_libelles) > 0) {
	      $this->addPoint('erreur', 'doublon_produits', implode(",", $doublon_libelles), $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'produits'))); 
	    }
	    if (count($produits) > self::MAX_PRODUIT) {
	    	$this->addPoint('erreur', 'nb_produits', '', $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'produits'))); 
	    }
  	}

  	public function getProduitsHashInError() {

  		return array_keys($this->produits_controle);
  	}
}