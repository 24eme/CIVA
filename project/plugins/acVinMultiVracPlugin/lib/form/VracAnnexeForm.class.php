<?php
class VracAnnexeForm extends acCouchdbObjectForm {

    protected $annexeid;

    public function __construct(acCouchdbJson $object, $annexeid, $options = array(), $CSRFSecret = null)
    {
        $this->annexeid = $annexeid;
        parent::__construct($object, $options, $CSRFSecret);
    }

	public function configure()
	{
        $this->setWidget('fichier', new sfWidgetFormInputFile(array('label' => 'Document')));
        $this->setValidator('fichier', new sfValidatorFile(array('required' => false, 'path' => sfConfig::get('sf_cache_dir'))));
		$this->widgetSchema->setNameFormat('[%s]');
	}

    protected function doUpdateObject($values)
    {
       parent::doUpdateObject($values);
       $file = (isset($values['fichier']))? $values['fichier'] : null;
       if ($file && !$file->isSaved()) {
           $file->save();
       }
       if ($file) {
           try {
               $this->getObject()->storeAnnexe($file->getSavedName(), $this->annexeid);
           } catch (sfException $e) {
               throw new sfException($e);
           }
           unlink($file->getSavedName());
       }
   }
}
