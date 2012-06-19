<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
{}
$t = new lime_test(6);

$configuration = ProjectConfiguration::getApplicationConfiguration( 'civa', 'test', true);
$databaseManager = new sfDatabaseManager($configuration);

if (!sfCouchdbManager::getClient()->databaseExists()) {
        sfCouchdbManager::getClient()->createDatabase();
 }

$rec = new Tiers();
$rec->_id = 'TIERS-TESTRECOLTANT';
$rec->siege->adresse = "2 rue du test";
$exploitant = $rec->get('exploitant');
$t->is($exploitant->getAdresse(), $rec->siege->adresse, "Si pas d'adresse de l'exploitant alors adresse du siege");
$t->is($exploitant->adresse, $rec->siege->adresse, "Same but with the attribute");
$exploitant->date_naissance = '1970-01-01';
$t->is($exploitant->date_naissance, '1970-01-01', 'date de naissance');
$t->is($exploitant->getDateNaissance(), '1970-01-01', 'date de naissance via get');
$t->is($exploitant->getDateNaissanceFr(), '01/01/1970', 'date de naissance via get en Fr');

//test la création du MDP
$pass = 'test';
$sshaPass = $rec->make_ssha_password($pass);
$verifPass = $rec->ssha_password_verify($sshaPass, 'test');
$t->ok($verifPass, 'creation et comparaison du mdp SSHA');



