<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
{}
$t = new lime_test(5);

$configuration = ProjectConfiguration::getApplicationConfiguration( 'civa', 'test', true);
$databaseManager = new sfDatabaseManager($configuration);

if (!sfCouchdbManager::getClient()->databaseExists()) {
        sfCouchdbManager::getClient()->createDatabase();
 }

$conf = sfCouchdbManager::getClient()->retrieveDocumentById('CONFIGURATION');
$t->ok($conf, 'document configuration exists');
$t->is($conf->get('recolte/appellation_GRDCRU/libelle'), 'AOC Alsace Grand Cru', 'Libelle on Grand Cru');
$t->is($conf->get('recolte/appellation_GRDCRU/rendement'), 61, "rendement Grand Cru");
$t->is($conf->get('recolte/appellation_GRDCRU/lieu30/cepage_MU/rendement'), $conf->get('recolte/appellation_GRDCRU/rendement'), "rendement inheritance");
$t->isnt($conf->get('recolte/appellation_GRDCRU/lieu25/cepage_MU')->getRendement(), $conf->get('recolte/appellation_GRDCRU')->getRendement(), "specific rendement");
