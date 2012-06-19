<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
{}
$t = new lime_test(6);

$configuration = ProjectConfiguration::getApplicationConfiguration( 'civa', 'test', true);
$databaseManager = new sfDatabaseManager($configuration);

if (!sfCouchdbManager::getClient()->databaseExists()) {
        sfCouchdbManager::getClient()->createDatabase();
 }

$msg = sfCouchdbManager::getClient()->retrieveDocumentById('MESSAGES');
$t->ok($msg, 'document messages exists');

$msg->add('my_test_message');
$msg->my_test_message = 'test';
$t->ok($msg->save(), 'can create test message');

$msg = sfCouchdbManager::getClient()->retrieveDocumentById('MESSAGES');
$t->is($msg->my_test_message, 'test', 'message saved');


$t->is(sfCouchdbManager::getClient('Messages')->getMessage('my_test_message'), 'test', 'retrieve direct message via client');




$t->ok($msg->remove('my_test_message'), 'message removed');

try
{
  $t->isnt($msg->my_test_message, 'test', 'cannot get deleted message');
}catch(Exception $e) 
{
  $t->pass('cannot get deleted message');
}

