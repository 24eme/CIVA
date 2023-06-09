<?php
class VracAnnexeForm extends acCouchdbObjectForm {

	public function configure()
	{
        $this->setWidget('libelle', new sfWidgetFormInputText());
        $this->setValidator('libelle', new sfValidatorString(array('required' => false)));
        $this->setWidget('fichier', new sfWidgetFormInputFile(array('label' => 'Annexe applicable')));
        $this->setValidator('fichier', new sfValidatorFile(array('required' => false, 'path' => sfConfig::get('sf_cache_dir'))));
		$this->widgetSchema->setNameFormat('annexe[%s]');
	}

    protected function doUpdateObject($values)
    {
       parent::doUpdateObject($values);
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
               $this->getObject()->storeAnnexe($file->getSavedName(), VracClient::VRAC_PREFIX_ANNEXE.strtolower(KeyInflector::slugify($libelle)));
           } catch (sfException $e) {
               throw new sfException($e);
           }
           unlink($file->getSavedName());
       }
   }
}
