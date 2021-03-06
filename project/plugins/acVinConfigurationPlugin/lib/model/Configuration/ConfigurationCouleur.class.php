<?php
/**
 * Model for ConfigurationCouleur
 *
 */

class ConfigurationCouleur extends BaseConfigurationCouleur {

	const TYPE_NOEUD = 'couleur';

    public function getChildrenNode() {

      return $this->cepages;
    }

    /**
     *
     * @return ConfigurationLieu
     */
    public function getLieu() {
        return $this->getParentNode();
    }

	public function getMention() {
        return $this->getLieu()->getMention();
    }

    public function hasCepage() {
    	return (count($this->cepages) > 1 || (count($this->cepages) == 1 && $this->cepages->getFirst()->getKey() != Configuration::DEFAULT_KEY));
    }

    public function setDonneesCsv($datas) {
      parent::setDonneesCsv($datas);

    	$this->getLieu()->setDonneesCsv($datas);
    	$this->libelle = ($datas[ProduitCsvFile::CSV_PRODUIT_COULEUR_LIBELLE])? $datas[ProduitCsvFile::CSV_PRODUIT_COULEUR_LIBELLE] : null;
      $this->code = $this->formatCodeFromCsv($datas[ProduitCsvFile::CSV_PRODUIT_COULEUR_CODE]);

      $this->setDroitDouaneCsv($datas, ProduitCsvFile::CSV_PRODUIT_COULEUR_CODE_APPLICATIF_DROIT);
      $this->setDroitCvoCsv($datas, ProduitCsvFile::CSV_PRODUIT_COULEUR_CODE_APPLICATIF_DROIT);

      $this->setDepartementCsv($datas);
    }

  	public function hasDepartements() {
  		return false;
  	}
  	public function hasDroits() {
  		return true;
  	}
  	public function hasLabels() {
  		return false;
  	}
  	public function hasDetails() {
  		return false;
  	}
  	public function getTypeNoeud() {
  		return self::TYPE_NOEUD;
  	}

	public function getRendementNoeud() {

		return $this->getRendementCouleur();
	}
}
