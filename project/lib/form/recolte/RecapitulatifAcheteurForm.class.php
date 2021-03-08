<?php
    class RecapitulatifAcheteurForm extends BaseForm     {

        protected $acheteur = null;

        public function __construct($acheteur, $defaults = array(), $options = array(), $CSRFSecret = null)
        {
            $this->acheteur = $acheteur;
            parent::__construct($defaults, $options, $CSRFSecret);
        }

        public function configure() {
	       $this->setWidgets(array(
				  'superficie' => new sfWidgetFormInputFloat(),
				  'dontdplc' => new sfWidgetFormInputFloat(),
				  )
			    );
            $this->setValidators(array(
                'superficie' => new sfValidatorNumber(array('required' => false)),
                'dontdplc' => new sfValidatorNumber(array('required' => false, 'max' => round($this->acheteur->volume, 2))),
            ));

            if($this->acheteur->getNoeud()->canHaveVci()) {
                $this->setWidget('dontvci', new sfWidgetFormInputFloat());
                $this->setValidator('dontvci', new sfValidatorNumber(array('required' => false)));
            }

            $this->widgetSchema->setLabel('superficie', "Superficie (".$this->acheteur->nom.")");
            $this->widgetSchema->setLabel('dontdplc', "Dont dépassement (".$this->acheteur->nom.")");

            $this->getValidator('dontdplc')->setMessage('max', "Le volume déclaré en dont dépassement de %value% hl ne peut être supérieur au volume vendu de %max% hl");

            $this->widgetSchema->setNameFormat('acheteur[%s]');
        }
    }
