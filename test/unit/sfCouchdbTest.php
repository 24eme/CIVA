<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
{}
$t = new lime_test(23);

$configuration = ProjectConfiguration::getApplicationConfiguration( 'civa', 'test', true);
$databaseManager = new sfDatabaseManager($configuration);

if (!sfCouchdbManager::getClient()->databaseExists()) {
        sfCouchdbManager::getClient()->createDatabase();
 }

$doc = new DR();
$doc->_id = 'TESTCOUCHDB';
try{
$t->ok($doc->save(), 'save an empty document');
}catch(Exception $e) {
  $t->fail('save an empty document '.$e);
 }
$t->is($doc->_id, 'TESTCOUCHDB', 'id is the good one');
$t->ok($doc->_rev, 'should have now a rev number');
$t->is(sfCouchdbManager::getClient()->getDoc('TESTCOUCHDB')->_rev, $doc->_rev, 'retrieve the new doc');
try {
$t->is($doc->type, 'DR', 'should have a type');
}catch(Exception $e) {
$t->fail('should have a type');
 }

$rev = $doc->_rev;
$doc->cvi = "TEST";
$doc->campagne = "2009";
$doc->save();

$t->is(sfCouchdbManager::getClient()->getDoc('TESTCOUCHDB')->cvi, 'TEST', 'cvi number saved');
$t->is(sfCouchdbManager::getClient()->getDoc('TESTCOUCHDB')->campagne, '2009', 'campagne saved');
$t->isnt(sfCouchdbManager::getClient()->getDoc('TESTCOUCHDB')->_rev, $rev, 'revision changed');

$detail = new DRRecolteAppellationCepageDetail();
$detail->setAppellation("1");
$detail->setCepage("PB");
$detail->setCodeLieu("lieu");
$detail->setSurface(100);
$detail->setVolume(10);
$detail->setCaveParticuliere(5);
$acheteur = $detail->getAcheteurs()->add();
$acheteur->setCvi("CVI_FICTIF");
$acheteur->setQuantiteVendue(5);
$t->ok($doc->addRecolte($detail), 'add detail');

$obj = $doc->getRecolte()->get('appellation_1')->get('lieu')->get('cepage_PB');
$t->ok($obj, 'can retrieve detail object');

$t->is($val = $doc->get('recolte/appellation_1/lieu/cepage_PB/detail/0/surface'), 100, 'can retrieve surface by hash');

$doc->set('recolte/appellation_1/lieu/cepage_PB/detail/0/surface', 150);
$rev = $doc->_rev;
$t->is($obj_hash = $doc->get('recolte/appellation_1/lieu/cepage_PB/detail/0/surface'), 150, 'can change value in the tree');
$doc->save();
$t->isnt($rev, $doc->_rev, 'revision number has changed after saving');

$doc->getRecolte()->addAppellation(2);
$iterator_ok = true;
$iterator_nb = 0;
foreach($doc->getRecolte() as $key => $item) {
    if (!($item instanceof sfCouchdbJson)) {
        $iterator_ok = false;
    } else {
        $iterator_nb++;
    }
}
$t->ok($iterator_ok && $iterator_nb == 2, 'Iterate : can foreach');
$t->ok($obj_array_access = $doc['recolte']['appellation_1']['lieu']['cepage_PB']['detail'][0], 'ArrayAccess : can get value in the tree');
$obj_array_access['denomination'] = 'test';
$t->is($obj_array_access->get('denomination'), 'test', 'ArrayAccess : can set value in the tree');
$t->is($doc->getRecolte()->count(), 2, 'ArrayAccess : can count');

$t->ok($doc->remove('recolte/appellation_1/lieu/cepage_PB/detail/0/surface'), 'remove a field');
 try{
   $t->ok(!$doc->get('recolte/appellation_1/lieu/cepage_PB/detail/0/surface'), 'field removed');
}catch(Exception $e) {
  $t->pass('field removed');
 }

$t->ok($doc->remove('recolte/appellation_1/lieu/cepage_PB/detail/0'), 'remove a multifield');
 try{
   $t->ok(!$doc->get('recolte/appellation_1/lieu/cepage_PB/detail/0'), 'field removed');
}catch(Exception $e) {
  $t->pass('multifield removed');
 }

$t->ok($doc->delete(), 'delete the document');
try
{
  sfCouchdbManager::getClient()->getDoc('TESTCOUCHDB');
  $t->fail('cannot retrieve delete doc');
}catch(Exception $e) 
{
  $t->pass('cannot retrieve delete doc');
}
