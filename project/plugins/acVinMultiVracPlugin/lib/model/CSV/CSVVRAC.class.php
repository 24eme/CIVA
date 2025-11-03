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

    /**
     * Ajoute une annexe à chaque Vrac du tableau $imported
     *
     * @param $annexe L'annexe à ajouter
     * @param $name Le nom de l'annexe à ajouter
     */
    public function addAnnexe($annexe = null, $name = 'Annexe_contrat')
    {
        if (! $annexe) {
            return;
        }

        $name_cleaned = $this->cleanAnnexeName($name);
        $this->storeAttachment($annexe, mime_content_type($annexe), $name_cleaned);
    }

    /**
     * Liste les annexes de l'objet
     * Le fichier csv importé est exclu
     *
     * @return array<string,string> Un tableau avec en clé le nom de l'annexe et en valeur son uri
     */
    public function getAnnexes()
    {
        $annexes = [];
        foreach($this->_attachments as $name => $data) {
            if (strpos($name, 'annexe_') !== 0) {
                continue;
            }

            $annexes[$name] = $this->getAttachmentUri($name);
        }

        return $annexes;
    }

    /**
     * Slugify le nom de l'annexe pour homogénisation
     *
     * @param $name Le nom de l'annexe
     * @return string
     */
    private function cleanAnnexeName($name)
    {
        $annexe = 'annexe_';

        $dot = strrpos($name, '.');
        $extension = ($dot !== false) ? substr($name, $dot) : '';
        $basename = str_replace($extension, '', $name);
        $basename = str_replace('annexe_', '', $basename);

        $annexe .= strtolower(
            KeyInflector::slugify($basename).$extension
        );

        return $annexe;
    }

}
