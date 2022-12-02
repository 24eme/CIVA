<?php

class SVStockageForm extends acCouchdbForm
{
    public function __construct(acCouchdbDocument $doc, $defaults = array(), $options = array(), $CSRFSecret = null) {
        parent::__construct($doc, $defaults, $options, $CSRFSecret);
    }

    public function configure()
    {
        $formProduits = new BaseForm();

        $defaults = [];
        foreach($this->getDocument()->getRecapProduits() as $hash => $produit) {
            $formStockage = new BaseForm();
            foreach($this->getDocument()->stockage as $stockage) {
                $produits = $stockage->getProduits();
                $formStockage->setWidget($stockage->numero, new bsWidgetFormInputFloat(array(), array('placeholder' => 'hl', 'class' => 'form-control text-right input-float input-sm input_volume_revendique')));
                $formStockage->setWidget($stockage->numero, new bsWidgetFormInputFloat(array(), array('placeholder' => 'hl', 'class' => 'form-control text-right input-float input-sm')));
                $formStockage->setValidator($stockage->numero, new sfValidatorNumber(array('required' => false)));

                if(isset($produits[$hash])) {
                    $defaults["produits"][$hash][$stockage->numero] = $produits[$hash];
                }
                if($stockage->isPrincipale()) {
                    $formStockage->widgetSchema[$stockage->numero]->setAttribute('readonly', 'readonly');
                    $formStockage->widgetSchema[$stockage->numero]->setAttribute('class', $formStockage->widgetSchema[$stockage->numero]->getAttribute('class').' principal');
                } else {
                    $formStockage->widgetSchema[$stockage->numero]->setAttribute('class', $formStockage->widgetSchema[$stockage->numero]->getAttribute('class').' secondaire');
                }
            }

            $formProduits->embedForm($hash, $formStockage);
        }

        $this->embedForm('produits', $formProduits);

        $this->setDefaults($defaults);
        $this->widgetSchema->setNameFormat('sv_stockage[%s]');
    }

    public function save() {
        $values = $this->getValues();

        foreach($this->getDocument()->stockage as $stockage) {
            $stockage->remove('produits');
            $stockage->add('produits');
        }

        foreach($values['produits'] as $hash => $volumes) {
            foreach($volumes as $numero => $volume) {
                $stockage = $this->getDocument()->stockage->get($numero);

                if($stockage->isPrincipale()) {
                    continue;
                }

                if(!$volume) {
                    continue;
                }

                $stockage->produits->add($hash, $volume);
            }
        }

        $this->getDocument()->save();
    }
}