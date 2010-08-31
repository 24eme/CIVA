<?php
class ldap {

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

    public function ldapAdd($recoltant) {
        print_r($recoltant); exit();

        if($recoltant){
            $ldapConnect = $this->ldapConnect();
            if($ldapConnect) {
                // prepare les données
                $identifier            = 'uid='.$recoltant->cvi.',ou=People,'.$this->ldapdc;
                $info['uid']           = $recoltant->cvi;
                $info['sn']            = $recoltant->nom;
                $info['cn']            = $recoltant->nom;
                $info['givenName']     = 'prenom';
                $info['objectClass'][0]   = 'top';
                $info['objectClass'][1]   = 'person';
                $info['objectClass'][2]   = 'posixAccount';
                $info['objectClass'][3]   = 'inetOrgPerson';
                $info['userPassword']  = $recoltant->mot_de_passe;
                $info['loginShell']    = '/bin/bash';
                $info['uidNumber']     = '1000';
                $info['gidNumber']     = '1000';
                $info['homeDirectory'] = '/home/'.$recoltant->cvi;
                $info['gecos']         = 'Mon recoltant,,,';
                $info['mail']          = $recoltant->email;
                $info['postalAddress'] = $recoltant->siege->adresse;
                $info['postalCode']    = $recoltant->siege->code_postal;
                $info['l']             = $recoltant->siege->commune;

                // Ajoute les données au dossier
                $r=ldap_add($ldapConnect, $identifier, $info);
                ldap_unbind($ldapConnect);
                return $r;
            }
        }
        return false;
    }
    
    public function ldapModify($recoltant, $values) {

        if($recoltant && $values){
            $ldapConnect = $this->ldapConnect();
            if($ldapConnect) {

                // prepare les données
                $identifier            = 'uid='.$recoltant->cvi.',ou=People,'.$this->ldapdc;
                if(isset($values['nom']))           $info['sn']            = $values['nom'];
                if(isset($values['nom']))           $info['cn']            = $values['nom'];
                if(isset($values['mot_de_passe']))  $info['userPassword']  = $values['mot_de_passe'];

                $info['gecos']         = 'Mon recoltant,,,';
                
                if(isset($values['mail']))          $info['mail'] = $values['email'];
                if(isset($values['adresse']))       $info['postalAddress'] = $values['adresse'];
                if(isset($values['code_postal']))   $info['postalCode']    = $values['code_postal'];
                if(isset($values['ville']))         $info['l']             = $values['ville'];

                // Ajoute les données au dossier
                $r=ldap_modify($ldapConnect, $identifier, $info);
                ldap_unbind($ldapConnect);
                return $r;
            }

        }
        return false;
    }

    public function ldapDelete($recoltant) {
        $ldapConnect = $this->ldapConnect();
        
        if($ldapConnect && $recoltant) {
            $identifier  = 'uid='.$recoltant->cvi.',ou=People,'.$this->ldapdc;
            $delete      = ldap_delete($ldapConnect, $identifier);
            ldap_unbind($ldapConnect);
            return $delete;
        }else{
            return false;
        }
    }

    public function ldapVerifieExistence($recoltant){
        $ldapConnect = $this->ldapConnect();
        if($ldapConnect && $recoltant) {
            $filter = 'uid='.$recoltant->cvi;
            return ldap_search($ldapConnect, 'ou=People,'.$this->ldapdc, $filter);
        }
    }

}

?>
