<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
{}
$t = new lime_test(6);

$configuration = ProjectConfiguration::getApplicationConfiguration( 'civa', 'test', true);
$databaseManager = new sfDatabaseManager($configuration);

if (!sfCouchdbManager::getClient()->databaseExists()) {
        sfCouchdbManager::getClient()->createDatabase();
 }

$rec = new Recoltant();
$rec->_id = 'REC-TESTRECOLTANT';
$rec->siege->adresse = "2 rue du test";
$exploitant = $rec->get('exploitant');
$t->is($exploitant->getAdresse(), $rec->siege->adresse, "Si pas d'adresse de l'exploitant alors adresse du siege");
$t->is($exploitant->adresse, $rec->siege->adresse, "Same but with the attribute");
$exploitant->date_naissance = '01/01/1970';
$t->is($exploitant->date_naissance, '01/01/1970', 'date de naissance');
$t->is($exploitant->getDateNaissance(), '01/01/1970', 'date de naissance via get');
$exploitant->date_naissance = '1970-01-01';
$t->is($exploitant->date_naissance, '01/01/1970', 'date de naissance via format DB2');
$t->is($exploitant->getDateNaissance(), '01/01/1970', 'date de naissance via format DB2 et get');

