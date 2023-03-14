<?php
class VracConditionsForm extends acCouchdbObjectForm
{

	public function configure()
    {
		$this->setWidgets(array(
        	'conditions_paiement' => new sfWidgetFormInputText(),
        	'conditions_particulieres' => new sfWidgetFormInputText(),
    	));

        $this->widgetSchema->setLabels(array(
        	'conditions_paiement' => 'Délais de paiement :',
        	'conditions_particulieres' => 'Autres clauses particulières :',
        ));

		$this->setValidators(array(
        	'conditions_paiement' => new sfValidatorString(array('required' => false)),
        	'conditions_particulieres' => new sfValidatorString(array('required' => false)),
        ));

		if($this->getObject()->exist('clause_reserve_propriete')) {
			$this->setWidget('clause_reserve_propriete', new sfWidgetFormChoice(array('choices' => ["1" => "Oui", "0" => "Non"], 'expanded' => true)));
			$this->setValidator('clause_reserve_propriete', new sfValidatorChoice(array('choices' => [1,0], 'required' => false)));
			$this->getWidgetSchema()->setLabel('clause_reserve_propriete', "Clause de réserve de propriété");
		}

		if($this->getObject()->exist('clause_mandat_facturation')) {
			$this->setWidget('clause_mandat_facturation', new sfWidgetFormChoice(array('choices' => ["1" => "Oui", "0" => "Non"], 'expanded' => true)));
			$this->setValidator('clause_mandat_facturation', new sfValidatorChoice(array('choices' => [1,0], 'required' => false)));
			$this->getWidgetSchema()->setLabel('clause_mandat_facturation', "Mandat de facturation");
		}

		if($this->getObject()->exist('vendeur_frais_annexes')) {
			$this->setWidget('vendeur_frais_annexes', new sfWidgetFormTextarea());
			$this->setValidator('vendeur_frais_annexes', new sfValidatorString(array('required' => false)));
			$this->getWidgetSchema()->setLabel('vendeur_frais_annexes', "Frais annexes en sus à la charge du vendeur");
		}

		if($this->getObject()->exist('acheteur_primes_diverses')) {
			$this->setWidget('acheteur_primes_diverses', new sfWidgetFormTextarea());
			$this->setValidator('acheteur_primes_diverses', new sfValidatorString(array('required' => false)));
			$this->getWidgetSchema()->setLabel('acheteur_primes_diverses', "Primes diverses à la charge de l'acheteur");
		}

		if($this->getObject()->exist('clause_resiliation')) {
			$this->setWidget('clause_resiliation', new sfWidgetFormTextarea());
			$this->setValidator('clause_resiliation', new sfValidatorString(array('required' => false)));
			$this->getWidgetSchema()->setLabel('clause_resiliation', "Résiliation hors cas de force majeur");
		}

		if($this->getObject()->exist('clause_evolution_prix')) {
			$this->setWidget('clause_evolution_prix', new sfWidgetFormTextarea());
			$this->setValidator('clause_evolution_prix', new sfValidatorString(array('required' => false)));
			$this->getWidgetSchema()->setLabel('clause_evolution_prix', "Critères et modalités d'évolution des prix pour les années N+1 et N+2");
		}

		if($this->getObject()->exist('nb_jour_apres_recolte_retiraison')) {
			$this->setWidget('nb_jour_apres_recolte_retiraison', new sfWidgetFormInputFloat());
			$this->setValidator('nb_jour_apres_recolte_retiraison', new sfValidatorNumber(array('required' => false)));
			$this->getWidgetSchema()->setLabel('nb_jour_apres_recolte_retiraison', "Délai maximum de retiraison");
		}

        if($this->getObject()->needDateRetiraison()) {
            $produitsRetiraisons = new VracRetiraisonsCollectionForm($this->getObject()->declaration->getActifProduitsDetailsSorted());
            $this->embedForm('produits_retiraisons', $produitsRetiraisons);
        }

        $this->widgetSchema->setNameFormat('vrac_conditions[%s]');
    }

    public function doUpdateObject($values) {
    	parent::doUpdateObject($values);
        if ($this->getObject()->exist('clause_reserve_propriete'))
            $this->getObject()->clause_reserve_propriete = (isset($values['clause_reserve_propriete']) && $values['clause_reserve_propriete'])? 1 : 0;
        if ($this->getObject()->exist('clause_mandat_facturation'))
            $this->getObject()->clause_mandat_facturation = (isset($values['clause_mandat_facturation']) && $values['clause_mandat_facturation'])? 1 : 0;
        foreach ($this->getEmbeddedForms() as $key => $embedForm) {
            $embedForm->doUpdateObject($values[$key]);
        }
    }
}
