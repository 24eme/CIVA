<?php

$COUCHTEST = (getenv('COUCHTEST')) ?: 'civa_test';

$couchClient = new couchClient(
    'http://localhost:5984/',
    $COUCHTEST
);

if (strpos($couchClient->getDatabaseName(), '_test') === false) {
    die("La connection à la base ne semble pas être une base de test. [".$couchClient->getDatabaseName()."]".PHP_EOL);
}

if ($couchClient->databaseExists()) {
    $couchClient->deleteDatabase();
}

$couchClient->createDatabase();

////

$configuration = ProjectConfiguration::getApplicationConfiguration('civa', 'test', true);
@sfContext::createInstance($configuration);

$db = sfContext::getInstance()->getDatabaseManager();
$db->setDatabase('default', new acCouchdbDatabase([
    'dsn' => 'http://localhost:5984/',
    'dbname' => $COUCHTEST
]));

$connection = $db->getDatabase()->getConnection();

if (strpos($connection->getDatabaseName(), '_test') === false) {
    die("La connection à la base ne semble pas être une base de test. [".$connection->getDatabaseName()."]".PHP_EOL);
}

foreach (glob(__DIR__.'/../data/*.json') as $file) {
    $doc = $connection->storeDoc(json_decode(file_get_contents($file)));
}
