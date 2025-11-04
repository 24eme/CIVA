<?php

/**
 * Model for CSV
 *
 */
class CSVVRAC extends BaseCSVVRAC
{
    public function __construct()
    {
        parent::__construct();
        $this->type = "CSVVRAC";
    }

    public function getFile()
    {
        return $this->getAttachmentUri($this->getFileName());
    }

    public function getFileContent()
    {
        return file_get_contents($this->getAttachmentUri($this->getFileName()));
    }

    public function getFileName()
    {
        return 'import_edi_' . $this->identifiant . '.csv';
    }

    public function hasErreurs()
    {
        return count($this->erreurs);
    }

    public function getErreurs($line = null)
    {
        if ($line === null) {
            return $this->_get('erreurs');
        }

        return array_filter($this->_get('erreurs')->toArray(), function ($v) use ($line) {
            return $v->num_ligne === $line;
        });
    }

    public function addErreur($erreur)
    {
        $erreurNode = $this->erreurs->add();
        $erreurNode->num_ligne = $erreur->num_ligne;
        $erreurNode->csv_erreur = $erreur->erreur_csv;
        $erreurNode->diagnostic = $erreur->raison;
        return $erreurNode;
    }

    public function clearErreurs()
    {
        $this->remove('erreurs');
        $this->add('erreurs');
        $this->statut = null;
    }
}
