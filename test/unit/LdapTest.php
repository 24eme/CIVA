<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
$t = new lime_test(2);

$ldap = new ldap();

//test de la connexion au LDAP
$connection = $ldap->ldapConnect();
$t->ok($connection, 'connexion au LDAP');
ldap_unbind($connection); // on ferme la connexion ouverte avant d'en réouvrir une pour le prochain test

//Création d'un récoltant fictif pour faire les tests de mofifications et de supression
$recoltant = new Recoltant();
$recoltant->_id = 'REC-TESTRECOLTANT';
$recoltant->cvi = '6823700100';
$recoltant->nom = 'TEST Recoltant';
$recoltant->email = 'test@example.com';
$recoltant->mdp = $recoltant->make_ssha_password('password');

//ajout au LDAP
/*$add = $ldap->ldapAdd($recoltant);
$t->ok($add, 'ajout d\'un recoltant');

//modification
$newData = new Recoltant();
$newData->nom = 'TEST Modify';
$newData->email = 'modify@example.com';
$newData->mdp = $recoltant->make_ssha_password('modify');

$modify = $ldap->ldapModify($recoltant, $newData);
$t->ok($modify, 'modification d\'un recoltant');*/

//suppression du lDAP
$delete = $ldap->ldapDelete($recoltant);
$t->ok($delete, 'supression d\'un recoltant');

?>