<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
{}
$t = new lime_test(2);

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