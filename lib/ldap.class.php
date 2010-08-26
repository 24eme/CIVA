<?php
class ldap {

    protected $ldapserveur = "CHA.NGE._.ME";
    protected $ldapdn      = "cn=admin,dc=vinsdalsace,dc=pro";
    protected $ldapdc      = "dc=vinsdalsace,dc=pro";
    protected $ldappass    = "my_passw";

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
                $info['postalAddress'] = 'adresse';
                $info['postalCode']    = '75000';
                $info['l']             = 'ville';

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
                $info['sn']            = $values['nom'];
                $info['cn']            = $values['nom'];
                $info['userPassword']  = $values['mot_de_passe'];
                $info['gecos']         = 'Mon recoltant,,,';
                $info['mail']          = $values['email'];
                $info['postalAddress'] = 'adresse';
                $info['postalCode']    = '75000';
                $info['l']             = 'ville';

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

}

?>
