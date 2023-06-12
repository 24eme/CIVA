<?php
class VracAnnexeForm extends BaseForm {

    protected $object;

    public function __construct(acCouchdbJson $object, $defaults = array(), $options = array(), $CSRFSecret = null)
    {
        $this->object = $object;
        parent::__construct($defaults, $options, $CSRFSecret);
    }

	public function configure()
	{
        $this->setWidget('libelle', new sfWidgetFormInputText());
        $this->setValidator('libelle', new sfValidatorString(array('required' => false)));
        $this->setWidget('fichier', new sfWidgetFormInputFile(array('label' => 'Annexe applicable')));
        $this->setValidator('fichier', new sfValidatorFile(array('required' => false, 'path' => sfConfig::get('sf_cache_dir'))));
		$this->widgetSchema->setNameFormat('annexe[%s]');
	}

    public function save($con = null) {
        $values = $this->getValues();
        $file = (isset($values['fichier']))? $values['fichier'] : null;
        if ($file && !$file->isSaved()) {
            $file->save();
        }
        if ($file) {
            if ($values['libelle']) {
                $libelle = $values['libelle'];
            } else {
                $libelle = $file->getOriginalName();
                $pointPos = strpos($libelle, '.');
                if (strpos($libelle, '.') !== false) {
                    $libelle = substr($libelle, 0, $pointPos);
                }
            }
            try {
                $this->object->storeAnnexe($file->getSavedName(), VracClient::VRAC_PREFIX_ANNEXE.strtolower(KeyInflector::slugify($libelle)));
            } catch (sfException $e) {
                throw new sfException($e);
            }
            unlink($file->getSavedName());
        }
        $this->object->save();
    }

   public function hasUpload() {
	   return true;
   }
}
