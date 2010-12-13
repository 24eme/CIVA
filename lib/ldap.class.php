<?php
class ldap {

    private static $groupe2gid = array('declarant' => 1000, 'admin' => 1001, 'exterieur' => 1002);
    private static $gid2groupe = array('1000' => 'declarant', '1001' => 'admin', '1002' => 'exterieur');

    protected $ldapserveur;
    protected $ldapdn;
    protected $ldapdc;
    protected $ldappass;

    public function __construct(){
             $this->ldapserveur = sfConfig::get('app_ldap_serveur');
             $this->ldapdn = sfConfig::get('app_ldap_dn');
             $this->ldapdc = sfConfig::get('app_ldap_dc');
             $this->ldappass = sfConfig::get('app_ldap_pass');
    }

    public function ldapConnect() {

        //Connexion au serveur LDAP
        $ldapconn = ldap_connect($this->ldapserveur);
        ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

        if ($ldapconn) {
            //Connexion au serveur LDAP
            $ldapbind = ldap_bind($ldapconn, $this->ldapdn, $this->ldappass);

            // Identification
            if ($ldapbind)
                return $ldapconn;
            else
                return false;

        }else
            return false;
    }

    private function getLdapInfo($tiers, $groupe = null) {
      if (!$groupe) {
          $groupe = 'declarant';
      }
      $info = array();
      $info['uid']           = $tiers->cvi;
      $info['sn']            = $tiers->nom;
      $info['cn']            = $tiers->nom;
      $info['objectClass'][0]   = 'top';
      $info['objectClass'][1]   = 'person';
      $info['objectClass'][2]   = 'posixAccount';
      $info['objectClass'][3]   = 'inetOrgPerson';
      $info['userPassword']  = $tiers->mot_de_passe;
      $info['loginShell']    = '/bin/bash';
      $info['uidNumber']     = '1000';
      $info['gidNumber']     = self::$groupe2gid[$groupe];
      $info['homeDirectory'] = '/home/'.$tiers->cvi;
      $info['gecos']         = $tiers->cvi.','.$tiers->no_accises.','.$tiers->intitule.' '.$tiers->nom.','.$tiers->exploitant->nom;
      $info['mail']          = $tiers->email;
      $info['postalAddress'] = $tiers->getAdresse();
      $info['postalCode']    = $tiers->getCodePostal();
      $info['l']             = $tiers->getCommune();
      return $info;
    }

    public function ldapAdd($tiers, $groupe = null) {
        if($tiers){
            $ldapConnect = $this->ldapConnect();
            if($ldapConnect) {
                // prepare les données
                $identifier            = 'uid='.$tiers->cvi.',ou=People,'.$this->ldapdc;
		$info = $this->getLdapInfo($tiers, $groupe);
                // Ajoute les données au dossier
                $r=ldap_add($ldapConnect, $identifier, $info);
                ldap_unbind($ldapConnect);
                return $r;
            }
        }
        return false;
    }

    public function getGroupe($uid) {
        $ldapConnect = $this->ldapConnect();
        if($ldapConnect && $uid) {
            $filter = 'uid='.$uid;
            $search = ldap_search($ldapConnect, 'ou=People,'.$this->ldapdc, $filter);
            if($search){
	      $dn = ldap_get_entries($ldapConnect, $search);
	      if (isset(self::$gid2groupe[$dn[0]['gidnumber'][0]]))
		return self::$gid2groupe[$dn[0]['gidnumber'][0]];
	      else return false;
            }
            return false;
        }  
    }
    
    public function ldapModify($tiers, $groupe = null) {

        if($tiers){
            $ldapConnect = $this->ldapConnect();
            if($ldapConnect) {

                // prepare les données
                $identifier            = 'uid='.$tiers->cvi.',ou=People,'.$this->ldapdc;
		$info = $this->getLdapInfo($tiers, $groupe);
                // Ajoute les données au dossier
                $r=ldap_modify($ldapConnect, $identifier, $info);
                ldap_unbind($ldapConnect);
                return $r;
            }

        }
        return false;
    }

    public function ldapDelete($tiers) {
        $ldapConnect = $this->ldapConnect();
        
        if($ldapConnect && $tiers) {
            $identifier  = 'uid='.$tiers->cvi.',ou=People,'.$this->ldapdc;
            $delete      = ldap_delete($ldapConnect, $identifier);
            ldap_unbind($ldapConnect);
            return $delete;
        }else{
            return false;
        }
    }

    public function ldapVerifieExistence($tiers){
        $ldapConnect = $this->ldapConnect();
        if($ldapConnect && $tiers) {
            $filter = 'uid='.$tiers->cvi;
            $search = ldap_search($ldapConnect, 'ou=People,'.$this->ldapdc, $filter);
            if($search){
                $count = ldap_count_entries($ldapConnect, $search);
                if($count>0)
                    return true;
            }
            return false;
            
        }
    }

    public function getTiersFromLdap($uid) {
        $ldapConnect = $this->ldapConnect();
        if($ldapConnect) {
            $filter = 'uid='.$uid;
            $search = ldap_search($ldapConnect, 'ou=People,'.$this->ldapdc, $filter);
            if($search && ldap_count_entries($ldapConnect, $search) > 0){
                   $dn = ldap_get_entries($ldapConnect, $search);
                   $entrie = $dn[0];
                   $tiers = new Tiers();
                   $tiers->set('_id', 'TIERS-TEMP-'.$entrie['uid'][0]);
                   $tiers->cvi = $entrie['uid'][0];
                   $tiers->nom = $entrie['sn'][0];
                   $tiers->email =  $entrie['mail'][0];
                   $tiers->mot_de_passe = $entrie['userPassword'][0];
                   $tiers->setAdresse($entrie['postalAddress'][0]);
                   $tiers->setCommune($entrie['l'][0]);
                   $tiers->setCodePostal($entrie['postalcode'][0]);
                   return $tiers;
            }
            return false;
        }
        return false;
    }

    public function getEntriesByGroupe($groupe) {
        $ldapConnect = $this->ldapConnect();
        $result = array();
        if($ldapConnect) {
            $filter = 'gidNumber='.self::$groupe2gid[$groupe];
            $search = ldap_search($ldapConnect, 'ou=People,'.$this->ldapdc, $filter);
            if ($search) {
                $result = ldap_get_entries($ldapConnect, $search);
            } else {
                return $result;
            }
        }
        return $result;
    }

}

?>
