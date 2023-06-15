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
        $this->setValidator('fichier', new sfValidatorFile(array(
                'required' => false,
                'mime_types' => [
                    'image/jpeg',
                    'image/pjpeg',
                    'image/png',
                    'image/x-png',
                    'application/vnd.oasis.opendocument.spreadsheet',
                    'application/excel',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/x-msexcel',
                    'application/vnd.oasis.opendocument.text',
                    'application/msword',
                    'application/x-msword',
                    'application/pdf',
                    'application/x-pdf'
                ],
                'path' => sfConfig::get('sf_cache_dir')
        ), ['mime_types' => 'Seuls les fichiers au format doc, docx, pdf et images sont autorisés.']
        ));
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
