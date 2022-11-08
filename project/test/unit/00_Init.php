<?php

//// Création Base + init avec documents

$configuration = ProjectConfiguration::getApplicationConfiguration('civa', 'test', true);
@sfContext::createInstance($configuration);

$db = sfContext::getInstance()->getDatabaseManager();
acCouchdbManager::initializeClient($db->getDatabase('test')->getParameter('dsn'), $db->getDatabase('test')->getParameter('dbname'));
$db->setDatabase('default', $db->getDatabase('test'));

$connection = $db->getDatabase()->getConnection();

if (strpos($connection->getDatabaseName(), '_test') === false) {
    die("La connection à la base ne semble pas être une base de test. [".$connection->getDatabaseName()."]".PHP_EOL);
}

if ($connection->databaseExists()) {
    $connection->deleteDatabase();
}

$connection->createDatabase();

foreach (glob(__DIR__.'/../data/*.json') as $file) {
    $doc = $connection->storeDoc(json_decode(file_get_contents($file)));
}

//// Création des vues
$viewTask = new acCouchdbBuildViewTask(new sfEventDispatcher(), new sfFormatter());
$viewTask->run([], ['connection' => 'test']);
