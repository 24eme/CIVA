<?php

class EtablissementsDiff
{

    protected $etablissementsDb2;
    protected $etablissementsCouchdb;
    protected $diff;

    public function __construct() {
        $tiersCsv = new Db2Tiers2Csv(sfConfig::get('sf_root_dir')."/data/import/Tiers/Tiers-last");
        $keyIgnored = $this->getKeyIgnored();
        $this->etablissementsDb2 = $tiersCsv->getEtablissements();
        foreach($this->etablissementsDb2 as $id => $etablissement) {
            foreach($keyIgnored as $key) {
                $this->etablissementsDb2[$id][$key] = null;
            }
        }
        $this->etablissementsCouchdb = array();
        $results = EtablissementClient::getInstance()->startkey(array("INTERPRO-declaration"))
                            ->endkey(array("INTERPRO-declaration", array()))
                            ->reduce(false)
                            ->getView('etablissement', 'all');
        foreach($results->rows as $row) {
            $etablissement = EtablissementClient::getInstance()->find($row->id, acCouchdbClient::HYDRATE_JSON);
            if($etablissement->famille == "COURTIER") {
                continue;
            }

            $this->etablissementsCouchdb[$row->id] = EtablissementCsvFile::export($etablissement);

            if(isset($this->etablissementsCouchdb[$row->id]) && isset($this->etablissementsDb2[$row->id]) && $this->etablissementsCouchdb[$row->id][EtablissementCsvFile::CSV_STATUT] == $this->etablissementsDb2[$row->id][EtablissementCsvFile::CSV_STATUT] && $this->etablissementsCouchdb[$row->id][EtablissementCsvFile::CSV_STATUT] == EtablissementClient::STATUT_SUSPENDU) {
                unset($this->etablissementsDb2[$row->id]);
                unset($this->etablissementsCouchdb[$row->id]);
                continue;
            }

            if(isset($this->etablissementsDb2[$row->id])) {
                $this->etablissementsCouchdb[$row->id][EtablissementCsvFile::CSV_NUM_REPRISE] = $this->etablissementsDb2[$row->id][EtablissementCsvFile::CSV_NUM_REPRISE];
            }
            foreach($keyIgnored as $key) {
                $this->etablissementsCouchdb[$row->id][$key] = null;
            }
        }

        $this->diff = array_diff_assoc_recursive($this->etablissementsDb2, $this->etablissementsCouchdb);
    }

    public function getKeyIgnored() {

        return array(
                    EtablissementCsvFile::CSV_ID,
                    EtablissementCsvFile::CSV_TYPE,
                    EtablissementCsvFile::CSV_ID_SOCIETE,
                    EtablissementCsvFile::CSV_NOM_COURT,
                    EtablissementCsvFile::CSV_ADRESSE_COMPLEMENTAIRE_1,
                    EtablissementCsvFile::CSV_ADRESSE_COMPLEMENTAIRE_2,
                    EtablissementCsvFile::CSV_RECETTE_LOCALE,
                    EtablissementCsvFile::CSV_CARTE_PRO,
                    EtablissementCsvFile::CSV_REGION,
                    EtablissementCsvFile::CSV_NATURE_INAO,
                    EtablissementCsvFile::CSV_CEDEX,
                    EtablissementCsvFile::CSV_INSEE,
                    EtablissementCsvFile::CSV_PAYS,
                    EtablissementCsvFile::CSV_INSEE_DECLARATION,
                    EtablissementCsvFile::CSV_COMMUNE_DECLARATION,
                    EtablissementCsvFile::CSV_TEL_PERSO,
                    EtablissementCsvFile::CSV_MOBILE,
                    EtablissementCsvFile::CSV_WEB,
                    EtablissementCsvFile::CSV_COMMENTAIRE,
                    EtablissementCsvFile::CSV_EXPLOITANT_PAYS,
                    );
    }

    public function getEtablissementsDb2() {

        return $this->etablissementsDb2;
    }

    public function getEtablissementsCouchdb() {

        return $this->etablissementsCouchdb;
    }

    public function getDiff() {

        return $this->diff;
    }
}

function array_diff_assoc_recursive($array1, $array2) {
    $difference=array();
    foreach($array1 as $key => $value) {
        if( is_array($value) ) {
            if( !isset($array2[$key]) || !is_array($array2[$key]) ) {
                $difference[$key] = $value;
            } else {
                $new_diff = array_diff_assoc_recursive($value, $array2[$key]);
                if( !empty($new_diff) )
                    $difference[$key] = $new_diff;
            }
        } else if( !array_key_exists($key,$array2) || $array2[$key] != $value ) {
            $difference[$key] = $value;
        }
    }
    return $difference;
}
