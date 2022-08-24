<?php
class VracMercurialeForm extends acCouchdbObjectForm {

    const interne_choices = [1 => 'VRAI', 0 => 'FAUX'];

    public function setup() {
        $this->setWidgets(array(
            'interne' => new sfWidgetFormChoice(array('choices' => self::interne_choices)),
            'acheteur_type' => new sfWidgetFormChoice(array('choices' => AnnuaireClient::$annuaire_types), array('required'=>false)),
            'vendeur_type' => new sfWidgetFormChoice(array('choices' => AnnuaireClient::$annuaire_types), array('required'=>false)),

        ));
        $this->widgetSchema->setLabels(array(
	       'interne' => 'Est un contrat interne :',
	       'acheteur_type' => 'Type d\'acheteur :',
	       'vendeur_type' => 'Type de vendeur :',
        ));
        $this->widgetSchema->setDefaults(array(
	       'interne' => $this->getObject()->isInterne() * 1,
	       'acheteur_type' => $this->getObject()->acheteur_type,
	       'vendeur_type' => $this->getObject()->vendeur_type,
        ));
        $this->setValidators(array(
	       'interne' => new sfValidatorChoice(array('choices' => array_keys(self::interne_choices))),
           'acheteur_type' => new sfValidatorChoice(array('choices' => array_keys(AnnuaireClient::$annuaire_types)), array('required'=>false)),
           'vendeur_type' => new sfValidatorChoice(array('choices' => array_keys(AnnuaireClient::$annuaire_types)), array('required'=>false)),
        ));

        $this->widgetSchema->setNameFormat('vracMercuriales[%s]');
    }

    public function doUpdateObject($values) {
        if ($values['interne']) {
            $this->getObject()->add('interne');
            $this->getObject()->interne = true;
        }else{
            $this->getObject()->remove('interne');
        }
        $this->getObject()->vendeur_type = $values['vendeur_type'];
        $this->getObject()->acheteur_type = $values['acheteur_type'];
    }
}