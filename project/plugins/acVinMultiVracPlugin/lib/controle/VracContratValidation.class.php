<?php
class VracContratValidation extends DocumentValidation
{
    const PRIX_SEUIL = 50;
    const PRIX_SEUIL_BLOQUANT = 10;
    const MAX_PRODUIT = 9;

	protected $produits_controle = array();
	protected $annuaire = null;

	public function __construct($document, $annuaire = null)
    {
        $this->annuaire = $annuaire;
        parent::__construct($document);
        $this->noticeVigilance = false;
    }

  	public function configure()
  	{
        $this->addControle('erreur', 'nb_produits', 'Vous ne pouvez pas saisir plus de '.self::MAX_PRODUIT.' produits par contrat.');
  		$this->addControle('erreur', 'doublon_produits', 'Vous ne pouvez pas déclarer des produits identiques.');
        $this->addControle('vigilance', 'prix_litre', 'Le prix doit être exprimé en €/HL et non en €/L');
    	$this->addControle('erreur', 'prix_litre', 'Le prix doit être exprimé en €/HL et non en €/L');
    	$this->addControle('vigilance', 'presence_annuaire', "Ce soussigné n'est pas présent dans l'annuaire de l'initiateur du contrat, il le sera une fois ce contrat validé");
    	$this->addControle('erreur', 'label_non_saisi', 'Vous devez préciser les labels de vos produits.');
        $this->addControle('erreur', 'retiraisons_non_saisi', 'Vous devez préciser les dates début et limite de retiraisins pour l\'ensemble de vos produits.');
  	}

    public function controle()
  	{
  		$this->produits_controle = array();
		$doublon_libelles = array();
		$label_libelles = array();
		$produits = array();
        $retiraisons_manquantes = array();
	  	foreach ($this->document->declaration->getActifProduitsDetailsSorted() as $details) {
			foreach ($details as $detail) {
				if (!isset($produits[$detail->getCepage()->getHash()])) {
					$produits[$detail->getCepage()->getHash()] = array();
				}
				$produits[$detail->getCepage()->getHash()][] = $detail;
                if((!$detail->exist('nb_bouteille') || !$detail->nb_bouteille) && $detail->prix_unitaire < self::PRIX_SEUIL_BLOQUANT) {
                    $this->addPoint('erreur', 'prix_litre', $detail->getLibelle(), $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'produits')));
                } elseif((!$detail->exist('nb_bouteille') || !$detail->nb_bouteille) && $detail->prix_unitaire < self::PRIX_SEUIL) {
                    $this->addPoint('vigilance', 'prix_litre', $detail->getLibelle(), $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'produits')));
                }
				if ($detail->exist('label') && ($detail->label === null || $detail->label === "")) {
				    $label_libelles[] = $detail->getLibelle();
				}
				if (
                    ($detail->exist('retiraison_date_debut') && ($detail->retiraison_date_debut === null || $detail->retiraison_date_debut === "")) ||
                    ($detail->exist('retiraison_date_limite') && ($detail->retiraison_date_limite === null || $detail->retiraison_date_limite === "")))
                 {
				    $retiraisons_manquantes[] = $detail->getLibelle();
				}

			}
			foreach ($produits as $produit) {
				if (count($produit) > 1) {
					$produit_tmp = $produit;
					foreach ($produit as $key => $detail) {
						unset($produit_tmp[$key]);
						foreach ($produit_tmp as $d) {
							if ($detail->lieu_dit == $d->lieu_dit && $detail->vtsgn == $d->vtsgn && $detail->millesime == $d->millesime && $detail->denomination == $d->denomination && $detail->label == $d->label) {
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

        if(!$this->document->isAcheteurProprietaire() && $this->annuaire && !$this->annuaire->exist($this->document->acheteur_type."/".$this->document->acheteur_identifiant)) {
            $this->addPoint('vigilance', 'presence_annuaire', $this->document->acheteur->raison_sociale, $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'soussignes')));
        }

        if(!$this->document->isVendeurProprietaire() && $this->annuaire && !$this->annuaire->exist($this->document->vendeur_type."/".$this->document->vendeur_identifiant)) {
            $this->addPoint('vigilance', 'presence_annuaire', $this->document->vendeur->raison_sociale, $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'soussignes')));
        }

        if (count($label_libelles) > 0) {
            $this->addPoint('erreur', 'label_non_saisi', implode(",", $label_libelles), $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'produits')));
        }

        if (count($retiraisons_manquantes) > 0) {
            $this->addPoint('erreur', 'retiraisons_non_saisi', implode(",", $retiraisons_manquantes), $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'conditions')));
        }
  	}

  	public function getProduitsHashInError() {

  		return array_keys($this->produits_controle);
  	}
}
