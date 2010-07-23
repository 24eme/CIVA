<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
{}
$t = new lime_test(5);

$configuration = ProjectConfiguration::getApplicationConfiguration( 'civa', 'test', true);
$databaseManager = new sfDatabaseManager($configuration);
$connection = $databaseManager->getDatabase()->getConnection();

$t->ok($connection, 'can retrieve default couchdb database');

$dsn = $connection->getServerUri();
$couch = new couchClient($dsn, 'testcouchdb');
$t->ok(!$couch->databaseExists(), 'database should not exist');

try{
$t->ok($couch->createDatabase() ,'Create database');
}catch(Exception $e) {
  $t->fail('Create database');
 }

$t->ok($couch->deleteDatabase(), 'delete the database');
$t->ok(!$couch->databaseExists(), 'database should not exist anymore');
