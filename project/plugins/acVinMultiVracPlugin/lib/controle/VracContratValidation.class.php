<?php
class VracContratValidation extends DocumentValidation
{
    const PRIX_SEUIL = 50;
    const PRIX_SEUIL_BLOQUANT = 10;

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
  		$this->addControle('erreur', 'doublon_produits', 'Vous ne pouvez pas déclarer des produits identiques.');
        $this->addControle('vigilance', 'prix_litre', 'Le prix doit être exprimé en €/HL et non en €/L');
    	$this->addControle('erreur', 'prix_litre', 'Le prix doit être exprimé en €/HL et non en €/L');
    	$this->addControle('vigilance', 'presence_annuaire', "Ce soussigné n'est pas présent dans l'annuaire de l'initiateur du contrat, il le sera une fois ce contrat validé");
    	$this->addControle('erreur', 'label_non_saisi', 'Vous devez préciser les labels de vos produits.');
        $this->addControle('erreur', 'millesime_non_saisi', 'Vous devez préciser les millésimes de vos produits.');
        $this->addControle('erreur', 'retiraisons_non_saisi', 'Vous devez préciser les dates début et limite de retiraison pour l\'ensemble de vos produits.');

        $this->addControle('erreur', 'retiraisons_pb', 'La date limite de retiraison ne peut pas être antérieur à la date de début de retiraison');
        $this->addControle('erreur', 'retiraisons_limite_incoherence', 'Les dates limite de retiraison ne peuvent dépasser le 31/07');
        $this->addControle('erreur', 'vendeur_assujetti_tva_required', 'Vous devez préciser si le vendeur est assujetti à la tva ou non');
        $this->addControle('erreur', 'acheteur_assujetti_tva_required', 'Vous devez préciser si l\'acheteur est assujetti à la tva ou non ');
        $this->addControle('erreur', 'clause_reserve_propriete_required', 'Vous devez préciser la présence ou non d\'une clause de réserve de propriété');
        $this->addControle('erreur', 'clause_mandat_facturation_required', 'Vous devez préciser si le vendeur donne mandat de facturation ou non à l\'acheteur');
        $this->addControle('erreur', 'clause_evolution_prix_required', 'Vous devez préciser les critères et modalités d’évolution des prix');
        $this->addControle('erreur', 'clause_evolution_prix_incomplete', ' vous ne totalisez pas 100% de répartition des indicateurs des critères et modalités d\'évolution des prix');
        $this->addControle('erreur', 'conditions_paiement_required', 'Vous devez préciser les délais de paiement');
        $this->addControle('erreur', 'vtsgn_denomination', "La mention VT/SGN doit être précisée en ajoutant un nouveau produit et non en dénomination des produits de la liste");

  	}

    public function controle()
  	{
  		$this->produits_controle = array();
		$doublon_libelles = array();
		$label_libelles = array();
		$millesimes = array();
		$produits = array();
        $vtsgn = [];
        $retiraisons_manquantes = array();
        $retiraisons_incoherentes = array();
        $retiraisons_pb  = array();
	  	foreach ($this->document->declaration->getActifProduitsDetailsSorted() as $details) {
			foreach ($details as $detail) {
				if (!isset($produits[$detail->getCepage()->getHash()])) {
					$produits[$detail->getCepage()->getHash()] = array();
				}
				$produits[$detail->getCepage()->getHash()][] = $detail;
                if($this->document->getPrixUnite() == VracClient::PRIX_HL && $detail->prix_unitaire < self::PRIX_SEUIL_BLOQUANT) {
                    $this->addPoint('erreur', 'prix_litre', $detail->getLibelle(), $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'produits')));
                } elseif($this->document->getPrixUnite() == VracClient::PRIX_HL && $detail->prix_unitaire < self::PRIX_SEUIL) {
                    $this->addPoint('vigilance', 'prix_litre', $detail->getLibelle(), $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'produits')));
                }
				if (!$detail->exist('label') || $detail->label === null || $detail->label === "") {
				    $label_libelles[] = $detail->getLibelle();
				}
				if (
                    ($detail->exist('retiraison_date_debut') && ($detail->retiraison_date_debut === null || $detail->retiraison_date_debut === "")) ||
                    ($detail->exist('retiraison_date_limite') && ($detail->retiraison_date_limite === null || $detail->retiraison_date_limite === "")))
                 {
				    $retiraisons_manquantes[] = $detail->getLibelle();
				}

                $dateDebutRetiraison = ($this->document->isPluriannuelCadre() && $detail->retiraison_date_debut)? Vrac::getDateRetiraisonByCampagne($this->document->campagne, $detail->retiraison_date_debut) : $detail->retiraison_date_debut;
                $dateLimiteRetiraison = ($this->document->isPluriannuelCadre() && $detail->retiraison_date_limite)? Vrac::getDateRetiraisonByCampagne($this->document->campagne, $detail->retiraison_date_limite) : $detail->retiraison_date_limite;
                if ($dateDebutRetiraison && $dateLimiteRetiraison && $dateDebutRetiraison >  $dateLimiteRetiraison) {
                    $retiraisons_pb[] = $detail->getLibelle();
                }
                if ($dateLimiteRetiraison && ($dateLimiteRetiraison > substr($this->document->campagne, -4).'-07-31')) {
                    $retiraisons_incoherentes[] = $detail->getLibelle();
                }
				if (!$detail->millesime && !$this->document->isPluriannuelCadre()) {
				    $millesimes[] = $detail->getLibelle();
				}
                if ($detail->denomination && Configuration::hasRefVtSgnInLibelle($detail->denomination)) {
                    $vtsgn[] = $detail->getLibelle();
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

        if(!$this->document->isAcheteurProprietaire() && $this->annuaire && !$this->annuaire->exist($this->document->acheteur_type."/".$this->document->acheteur_identifiant)) {
            $this->addPoint('vigilance', 'presence_annuaire', $this->document->acheteur->raison_sociale, $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'soussignes')));
        }

        if(!$this->document->isVendeurProprietaire() && $this->annuaire && !$this->annuaire->exist($this->document->vendeur_type."/".$this->document->vendeur_identifiant)) {
            $this->addPoint('vigilance', 'presence_annuaire', $this->document->vendeur->raison_sociale, $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'soussignes')));
        }

        if (count($label_libelles) > 0) {
            $this->addPoint('erreur', 'label_non_saisi', implode(",", $label_libelles), $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'produits')));
        }

        if (count($millesimes) > 0) {
            $this->addPoint('erreur', 'millesime_non_saisi', implode(",", $millesimes), $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'produits')));
        }

        if($this->document->needDateRetiraison() && count($retiraisons_manquantes) > 0) {
            $this->addPoint('erreur', 'retiraisons_non_saisi', implode(",", $retiraisons_manquantes), $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'conditions')));
        }

        if (count($retiraisons_pb) > 0) {
            $this->addPoint('erreur', 'retiraisons_pb', implode(",", $retiraisons_pb), $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'conditions')));
        }

        if ($this->document->exist('vendeur_assujetti_tva') && $this->document->vendeur_assujetti_tva === null) {
            $this->addPoint('erreur', 'vendeur_assujetti_tva_required', 'vendeur assujetti tva', $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'conditions')));
        }

        if ($this->document->exist('acheteur_assujetti_tva') && $this->document->acheteur_assujetti_tva === null) {
            $this->addPoint('erreur', 'acheteur_assujetti_tva_required', 'acheteur assujetti tva', $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'conditions')));
        }

        if ($this->document->exist('clause_reserve_propriete') && $this->document->clause_reserve_propriete === null) {
            $this->addPoint('erreur', 'clause_reserve_propriete_required', 'clause reserve propriete', $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'conditions')));
        }

        if ($this->document->exist('clause_mandat_facturation') && $this->document->clause_mandat_facturation === null) {
            $this->addPoint('erreur', 'clause_mandat_facturation_required', 'clause mandat facturation', $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'conditions')));
        }

        if ($this->document->exist('clause_evolution_prix') && !$this->document->clause_evolution_prix) {
            $this->addPoint('erreur', 'clause_evolution_prix_required', 'Critères et modalités d\'évolution des prix', $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'conditions')));
        }

        if ($this->document->exist('conditions_paiement') && !$this->document->conditions_paiement) {
            $this->addPoint('erreur', 'conditions_paiement_required', 'Délais de paiement', $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'conditions')));
        }

        $totalPourcentage = $this->document->getPourcentageTotalDesClausesEvolutionPrix();
        if ($totalPourcentage != 100) {
            $this->addPoint('erreur', 'clause_evolution_prix_incomplete', 'clause evolution prix répartie à '.$totalPourcentage.'%', $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'conditions')));
        }
	    if (count($vtsgn) > 0) {
	      $this->addPoint('erreur', 'vtsgn_denomination', implode(",", $vtsgn), $this->generateUrl('vrac_etape', array('sf_subject' => $this->document, 'etape' => 'produits')));
	    }
  	}

  	public function getProduitsHashInError() {

  		return array_keys($this->produits_controle);
  	}
}
