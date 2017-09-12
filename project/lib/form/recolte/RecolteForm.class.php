<?php

class RecolteForm extends acCouchdbObjectForm {

    const FORM_NAME_NEGOCES = 'negoces';
    const FORM_NAME_COOPERATIVES = 'cooperatives';
    const FORM_NAME_MOUTS = 'mouts';
    const FORM_SUFFIX_NEW = '_new';
    const FORM_NAME = 'detail[%s]';

    public function configure() {
        $this->setWidgets(array(
            'lieu' => new sfWidgetFormInputText(),
            'denomination' => new sfWidgetFormInputText(),
            'vtsgn' => new sfWidgetFormSelect(array('choices' => $this->getChoicesVvtsgn())),
            'superficie' => new sfWidgetFormInputFloat(),
            'cave_particuliere' => new sfWidgetFormInputFloat(),
        ));

        $this->setValidators(array(
            'lieu' => new sfValidatorString(array('required' => false)),
            'denomination' => new sfValidatorString(array('required' => false)),
            'vtsgn' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getChoicesVvtsgn()))),
            'superficie' => new sfValidatorNumber(array('required' => false)),
            'cave_particuliere' => new sfValidatorNumber(array('required' => false)),
        ));

        if($this->getObject()->canHaveUsagesLiesSaisi()) {
            $this->setWidget('lies', new sfWidgetFormInputFloat());
            $this->setValidator('lies', new sfValidatorNumber(array('required' => false)));
            $this->getWidget('lies')->setAttribute('class', 'num lies');
        }

        if($this->getObject()->canHaveVci()) {
            $this->setWidget('vci', new sfWidgetFormInputFloat());
            $this->setValidator('vci', new sfValidatorNumber(array('required' => false)));
        }

        if ($this->getOption('lieu_required', false)) {
            $this->getValidator('lieu')->setOption('required', true);
        }

        if ($this->getOption('superficie_required', true)) {
            $this->getValidator('superficie')->setOption('required', true);
            $this->getValidator('superficie')->setOption('min', 0.01);
        }

        $this->validatorSchema['superficie']->setMessage('required', 'Champ obligatoire');

        $this->configureAcheteurs(self::FORM_NAME_NEGOCES, $this->getObject()->getVolumeAcheteurs('negoces'), $this->getAcheteursNegoces());
        $this->configureAcheteurs(self::FORM_NAME_COOPERATIVES, $this->getObject()->getVolumeAcheteurs('cooperatives'), $this->getAcheteursCooperatives());
        if ($this->hasAcheteursMouts()) {
            $this->getObject()->add('mouts');
            $this->configureAcheteurs(self::FORM_NAME_MOUTS, $this->getObject()->getVolumeAcheteurs('mouts'), $this->getAcheteursMouts());
        }

        $this->getValidatorSchema()->setPostValidator(new ValidatorRecolte(null, array('object' => $this->getObject(), 'has_acheteurs_mout' => $this->hasAcheteursMouts())));

        $this->widgetSchema->setNameFormat(self::FORM_NAME);
        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    }

    protected function updateDefaultsFromObject() {
        $defaults = $this->getDefaults();

        parent::updateDefaultsFromObject();

        $this->setDefaults($defaults + $this->getDefaults());
    }

    public function bind(array $taintedValues = null, array $taintedFiles = null) {
        $this->configureAcheteursFromBind(self::FORM_NAME_NEGOCES . self::FORM_SUFFIX_NEW, $taintedValues);
        $this->configureAcheteursFromBind(self::FORM_NAME_COOPERATIVES . self::FORM_SUFFIX_NEW, $taintedValues);

        if ($this->hasAcheteursMouts()) {
            $this->configureAcheteursFromBind(self::FORM_NAME_MOUTS . self::FORM_SUFFIX_NEW, $taintedValues);
        }
        parent::bind($taintedValues, $taintedFiles);
    }

    public static function getNewAcheteurItemAjax($name, $cvi) {
        $form_container = new BaseForm();
        $form_container->getWidgetSchema()->setNameFormat(self::FORM_NAME);
        $form = new BaseForm();
        $form->embedForm($cvi, new RecolteAcheteurForm(array('quantite_vendue' => null)));
        $form_container->embedForm($name . self::FORM_SUFFIX_NEW, $form);
        return $form_container[$name.self::FORM_SUFFIX_NEW][$cvi];
    }

    protected function configureAcheteurs($name, $values, $acheteurs) {
        $form = new BaseForm();
        foreach ($acheteurs as $cvi) {
            $quantite_vendue = null;
            if (isset($values[$cvi])) {
                $quantite_vendue = $values[$cvi];
            }
            $form->embedForm($cvi, new RecolteAcheteurForm(array('quantite_vendue' => $quantite_vendue)));
        }
        $this->embedForm($name, $form);
    }

    protected function configureAcheteursFromBind($name, $taintedValues) {
        if (isset($taintedValues[$name])) {
            $form = new BaseForm();
            foreach ($taintedValues[$name] as $cvi => $value) {
                $form->embedForm($cvi, new RecolteAcheteurForm(array('quantite_vendue' => $value['quantite_vendue'])));
            }
            $this->embedForm($name, $form);
        }
    }

    public function doUpdateObject($values) {
        if(!isset($values['vtsgn']) && $this->getObject()->getMention()->getKey() != 'mention') {
            $values['vtsgn'] = str_replace("mention", "", $this->getObject()->getMention()->getKey());
        }

        parent::doUpdateObject($values);

        $this->getObject()->negoces->clear();
        $this->getObject()->cooperatives->clear();
        if ($this->hasAcheteursMouts()) {
            $this->getObject()->mouts->clear();
        }

        $this->updateAcheteurs(self::FORM_NAME_NEGOCES, $values, $this->getObject()->negoces);
        $this->updateAcheteurs(self::FORM_NAME_NEGOCES . self::FORM_SUFFIX_NEW, $values, $this->getObject()->negoces, $this->getAcheteursNegoces());
        $this->updateAcheteurs(self::FORM_NAME_COOPERATIVES, $values, $this->getObject()->cooperatives);
        $this->updateAcheteurs(self::FORM_NAME_COOPERATIVES . self::FORM_SUFFIX_NEW, $values, $this->getObject()->cooperatives, $this->getAcheteursCooperatives());

        if ($this->hasAcheteursMouts()) {
            $this->updateAcheteurs(self::FORM_NAME_MOUTS, $values, $this->getObject()->mouts);
            $this->updateAcheteurs(self::FORM_NAME_MOUTS . self::FORM_SUFFIX_NEW, $values, $this->getObject()->mouts, $this->getAcheteursMouts());
        }

        $this->getObject()->getCouchdbDocument()->update();
    }

    protected function updateAcheteurs($value_name, $values, $object, $with_add_acheteurs = false) {
        if (isset($values[$value_name])) {
            foreach ($values[$value_name]as $cvi => $value) {
                if ($value['quantite_vendue'] > 0) {
                    $acheteur = $object->add();
                    $acheteur->cvi = $cvi;
                    $acheteur->quantite_vendue = $value['quantite_vendue'];
                    if ($with_add_acheteurs !== false) {
                        $this->getObject()->getCouchdbDocument()->get($with_add_acheteurs->getHash())->add(null, $cvi);
                    }
                }
            }
        }
    }

    protected function getAcheteursNegoces() {
        $acheteurs = $this->getOption('acheteurs_negoce', null);
        if (is_null($acheteurs)) {
            throw new sfException('Option "acheteurs_negoce" is required');
        }
        return $acheteurs;
    }

    protected function getAcheteursCooperatives() {
        $acheteurs = $this->getOption('acheteurs_cooperative', null);
        if (is_null($acheteurs)) {
            throw new sfException('Option "acheteurs_cooperative" is required');
        }
        return $acheteurs;
    }

    protected function getAcheteursMouts() {
        return $this->getOption('acheteurs_mout', null);
    }

    protected function hasAcheteursMouts() {
        return $this->getOption('has_acheteurs_mout', false);
    }

    protected function getChoicesVvtsgn() {
        return array('' => '', 'VT' => 'VT', 'SGN' => 'SGN');
    }

    public function updateObjectEmbeddedForms($values, $forms = null) {

    }

}
