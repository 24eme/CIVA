<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
{}
$t = new lime_test(7);

$databaseManager = new sfDatabaseManager($this->configuration);

if (!sfCouchdbManager::getClient()->databaseExists()) {
        sfCouchdbManager::getClient()->createDatabase();
 }

$doc = new sfCouchdbDocument();
$doc->_id = 'TESTCOUCHDB';
try{
$t->ok($doc->save(), 'save an empty document');
}catch(Exception $e) {
  $t->fail('save an empty document '.$e);
 }
$t->is($doc->_id, 'TESTCOUCHDB', 'id is the good one');
$t->ok($doc->_rev, 'should have now a rev number');
$t->is(sfCouchdbManager::getClient()->getDoc('TESTCOUCHDB')->_rev, $doc->_rev, 'retrieve the new doc');
$t->ok($doc->delete(), 'delete the document');
try
{
  sfCouchdbManager::getClient()->getDoc('TESTCOUCHDB');
  $t->fail('cannot retrieve delete doc');
}catch(Exception $e) 
{
  $t->pass('cannot retrieve delete doc');
}
