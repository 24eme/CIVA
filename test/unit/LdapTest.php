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
$recoltant = new Recoltant();
$recoltant->_id = 'REC-TESTRECOLTANT';
$recoltant->cvi = '680000000000';
$recoltant->nom = 'TEST Recoltant';
$recoltant->email = 'test@example.com';
$recoltant->mot_de_passe = $recoltant->make_ssha_password('password');

//ajout au LDAP
$add = $ldap->ldapAdd($recoltant);
$t->ok($add, 'ajout d\'un recoltant');

//modification
$newData = new Recoltant();
$newData->nom = 'TEST Modify';
$newData->email = 'modify@example.com';
$newData->mot_de_passe = $recoltant->make_ssha_password('modify');

$modify = $ldap->ldapModify($recoltant, $newData);
$t->ok($modify, 'modification d\'un recoltant');

//suppression du lDAP
$delete = $ldap->ldapDelete($recoltant);
$t->ok($delete, 'supression d\'un recoltant');

?>