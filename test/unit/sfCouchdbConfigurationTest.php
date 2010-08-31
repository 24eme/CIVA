<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
{}
$t = new lime_test(11);

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

/*** TEST FILTRE ***/
$t->is($conf->get('recolte/appellation_ALSACEBLANC/lieu')->filter('cepage')->getFirst()->getLibelle(), 'Pinot Gris', "direct key recuperation of first 'cepage' from an 'appellation'");
$conf->get('recolte/appellation_ALSACEBLANC/lieu')->filter('cepage', true);
$t->is($conf->get('recolte/appellation_ALSACEBLANC/lieu')->getFirstKey(), 'cepage_PG', "key recuperation of first 'cepage' from an 'appellation'");
$t->is($conf->get('recolte/appellation_ALSACEBLANC/lieu')->getFirst()->libelle, 'Pinot Gris', "libelle recuperation of first 'cepage' from an 'appellation'");
$iterator_ok = true;
$iterator_nb = 0;
foreach($conf->get('recolte/appellation_ALSACEBLANC/lieu') as $key => $item) {
    if (!($item instanceof sfCouchdbJson || !method_exists($item, 'getLibelle'))) {
        $iterator_ok = false;
    } else {
        $iterator_nb++;
    }
}
$t->ok($iterator_ok && $iterator_nb == 8,  "foreach on cepages with a filter");
$t->is($conf->get('recolte/appellation_ALSACEBLANC/lieu')->count(), 8,  "count cepages with a filter");
$conf->get('recolte/appellation_ALSACEBLANC/lieu')->clearFilter();
$t->is($conf->get('recolte/appellation_ALSACEBLANC/lieu')->count(), 12,  "clear filter");

$t->ok($conf->get('recolte/appellation_ALSACEBLANC/douane/type_aoc'), "Configuration Douane");

$t->is($conf->get('recolte/appellation_ALSACEBLANC/lieu/Cepage_GW/douane')->getCouleur(), 
       $conf->get('recolte/appellation_ALSACEBLANC/douane/couleur'), "Teste l'héritage des infos douanes");