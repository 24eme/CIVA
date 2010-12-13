<?php
class Tiers extends BaseTiers {
    protected $is_tiers_fictif = false;

    public function getDeclaration($campagne) {
        return sfCouchdbManager::getClient('DR')->retrieveByCampagneAndCvi($this->cvi, $campagne);
    }

    public function getDeclarationArchivesCampagne($campagne) {
        return sfCouchdbManager::getClient('DR')->getArchivesCampagnes($this->cvi, $campagne);
    }

    public function getDeclarations($hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
        return sfCouchdbManager::getClient('DR')->getAllByCvi($this->cvi, $hydrate);
    }

    public function isDeclarant() {
        return !($this->exist('no_declarant') && $this->get('no_declarant'));
    }

    public function getAdresse() {
        return $this->get('siege')->get('adresse');
    }
    public function getCodePostal() {
        return $this->get('siege')->get('code_postal');
    }
    public function getCommune() {
        return $this->get('siege')->get('commune');
    }
    public function setAdresse($a) {
        return $this->get('siege')->set('adresse', $a);
    }
    public function setCodePostal($c) {
        return $this->get('siege')->set('code_postal', $c);
    }
    public function setCommune($c) {
        return $this->get('siege')->set('commune', $c);
    }


    public function hasNoAssices(){
        if($this->get('no_accises')) return true;
        else return false;
    }

    public function make_ssha_password($password) {
        mt_srand((double)microtime()*1000000);
        $salt = pack("CCCC", mt_rand(), mt_rand(), mt_rand(), mt_rand());
        $hash = "{SSHA}" . base64_encode(pack("H*", sha1($password . $salt)) . $salt);
        return $hash;
    }

    public function ssha_password_verify($hash, $password) {
        // Verify SSHA hash
        $ohash = base64_decode(substr($hash, 6));
        $osalt = substr($ohash, 20);
        $ohash = substr($ohash, 0, 20);
        $nhash = pack("H*", sha1($password . $osalt));
        
        if ($ohash == $nhash) {
            return True;
        } else {
            return False;
        }
    }

    public function setIsTiersFictif($value) {
        $this->is_tiers_fictif = $value;
    }

    public function save() {
        if (!$this->is_tiers_fictif) {
            parent::save();
        }
    }

    public function delete() {
        if (!$this->is_tiers_fictif) {
            parent::delete();
        }
    }
}