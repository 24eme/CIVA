<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(4);

$configuration = ProjectConfiguration::getApplicationConfiguration( 'civa', 'test', true);

$ldap = new ldap();

//test de la connexion au LDAP
$connection = $ldap->ldapConnect();
$t->ok($connection, 'connexion au LDAP');
ldap_unbind($connection); // on ferme la connexion ouverte avant d'en réouvrir une pour le prochain test

//Création d'un récoltant fictif pour faire les tests de mofifications et de supression
$tiers = new Tiers();
$tiers->_id = 'REC-TESTRECOLTANT';
$tiers->cvi = '680000000000';
$tiers->nom = 'TEST Recoltant';
$tiers->email = 'test@example.com';
$tiers->mot_de_passe = $tiers->make_ssha_password('password');

$tiers->no_accises = 'FR000000E00';

/* siege */
$tiers->setAdresse('1 rue de test');
$tiers->setCodePostal('75000');
$tiers->setCommune('Test');

/* Exploitant */
$tiers->get('exploitant')->set('sexe', 'M');
$tiers->get('exploitant')->set('nom', $tiers->nom);
$tiers->get('exploitant')->set('adresse', '1 rue de test');
$tiers->get('exploitant')->set('code_postal', '00000');
$tiers->get('exploitant')->set('commune', 'test');
$tiers->get('exploitant')->set('date_naissance', '1970-01-01');
$tiers->get('exploitant')->set('telephone', '0102030405');
 
$verify = $ldap->ldapVerifieExistence($tiers);
if($verify){
    $delete = $ldap->ldapDelete($tiers);
}

//ajout au LDAP
$add = $ldap->ldapAdd($tiers);
$t->ok($add, 'ajout d\'un recoltant');

$groupe = $ldap->getGroupe($tiers);
print_r($groupe);

//modification
$newData = new Tiers();
$newData->nom = 'TEST Modify';
$newData->email = 'modify@example.com';
$newData->mot_de_passe = $tiers->make_ssha_password('modify');

$modify = $ldap->ldapModify($tiers, $newData);
$t->ok($modify, 'modification d\'un recoltant');

//suppression du lDAP
$delete = $ldap->ldapDelete($tiers);
$t->ok($delete, 'supression d\'un recoltant');

?>
