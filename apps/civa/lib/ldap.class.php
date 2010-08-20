<?php
class ldap {

    protected $ldapserveur = "CHA.NGE._.ME";
    protected $ldapdn      = "cn=admin,dc=vinsdalsace,dc=pro";
    protected $ldappass    = "my_passw";

    private function ldapConnect() {

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

        $ldapConnect = $this->ldapConnect();

        if($ldapConnect) {

            // prepare les données
            $info['dn']            = 'uid='.$recoltant->cvi.',ou=People,dc=vinsdalsace,dc=pro';
            $info['uid']           = $recoltant->cvi;
            $info['sn']            = $recoltant->nom;
            $info['cn']            = $recoltant->nom;
            $info['givenName']     = 'prenom';
            $info['objectClass']   = 'top';
            $info['objectClass']   = 'person';
            $info['objectClass']   = 'posixAccount';
            $info['objectClass']   = 'inetOrgPerson';
            $info['userPassword']  = $recoltant->mdp;
            $info['loginShell']    = '/bin/bash';
            $info['uidNumber']     = '1000';
            $info['gidNumber']     = '1000';
            $info['homeDirectory'] = '/home/'.$recoltant->cvi;
            $info['gecos']         = 'Mon recoltant,,,';
            $info['mail']          = $recoltant->email;
            $info['postalAddress'] = 'adresse';
            $info['postalCode']    = 'CP';
            $info['l']             = 'ville';
            
            // Ajoute les données au dossier
            $r=ldap_add($ldapConnect, "cn=admin,dc=vinsdalsace,dc=pro", $info);

        }
        ldap_unbind($ldapConnect);

        if($r)
            return $r;
        else
            return false;
    }
}

?>
