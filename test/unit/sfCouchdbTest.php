<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
{}
$t = new lime_test(47);

$configuration = ProjectConfiguration::getApplicationConfiguration( 'civa', 'test', true);
$databaseManager = new sfDatabaseManager($configuration);

if (!sfCouchdbManager::getClient()->databaseExists()) {
        sfCouchdbManager::getClient()->createDatabase();
 }
$doc = sfCouchdbManager::getClient()->retrieveDocumentById('TESTCOUCHDB');
if ($doc) 
  $doc->delete();

$doc = new DR();
$doc->_id = 'TESTCOUCHDB';
try{
$t->ok($doc->save(), 'save an empty document');
}catch(Exception $e) {
  $t->fail('save an empty document '.$e);
 }
/*** NEW TEST ****/
$t->is($doc->_id, 'TESTCOUCHDB', 'id is the good one');
/*** NEW TEST ****/
$t->ok($doc->_rev, 'should have now a rev number');
/*** NEW TEST ****/
/*** TEST 1 ****/
$t->is(sfCouchdbManager::getClient()->getDoc('TESTCOUCHDB')->_rev, $doc->_rev, 'retrieve and verify the new doc');
$dr = sfCouchdbManager::getClient()->retrieveDocumentById('TESTCOUCHDB');
$t->is(get_class($dr), 'DR', 'retrieve the doc as a DR object');
$t->is($dr->_rev, $doc->_rev, 'verify the DR object has the correct rev number');



/*** NEW TEST ****/
/*** TEST 1 ****/
try {
$t->is($doc->type, 'DR', 'should have a type');
}catch(Exception $e) {
$t->fail('should have a type');
 }
/*** NEW TEST ****/
$rev = $doc->_rev;
$doc->cvi = "TEST";
$doc->campagne = "2009";
$doc->save();

$t->is(sfCouchdbManager::getClient()->getDoc('TESTCOUCHDB')->cvi, 'TEST', 'cvi number saved');
/*** NEW TEST ****/
$t->is(sfCouchdbManager::getClient()->getDoc('TESTCOUCHDB')->campagne, '2009', 'campagne saved');
/*** NEW TEST ****/
$t->isnt(sfCouchdbManager::getClient()->getDoc('TESTCOUCHDB')->_rev, $rev, 'revision changed');

/*** NEW TEST ****/


$t->ok($detail = $doc->recolte->add('appellation_ALSACEBLANC')->add('lieu')->add('cepage_PG')->detail->add(), 'add detail');

$detail->setAppellation("ALSACEBLANC");
$detail->setCepage("PG");
$detail->setSuperficie(100);
$detail->setVolume(10);
$detail->setCaveParticuliere(5);
$acheteur = $detail->getNegoces()->add();
$acheteur->setCvi("CVI_FICTIF");
$acheteur->setQuantiteVendue(5);

$obj = $doc->getRecolte()->get('appellation_ALSACEBLANC')->get('lieu')->get('cepage_PG');
$t->ok($obj, 'can retrieve detail object');

/*** NEW TEST ****/
$t->is($val = $doc->get('recolte/appellation_ALSACEBLANC/lieu/cepage_PG/detail/0/superficie'), 100, 'can retrieve superficie by hash');

/*** NEW TEST ****/

$doc->set('recolte/appellation_ALSACEBLANC/lieu/cepage_PG/detail/0/superficie', 150);
$rev = $doc->_rev;
$t->is($obj_hash = $doc->get('recolte/appellation_ALSACEBLANC/lieu/cepage_PG/detail/0/superficie'), 150, 'can change value in the tree');

/*** NEW TEST ****/
$doc->save();
$t->isnt($rev, $doc->_rev, 'revision number has changed after saving');
/*** NEW TEST ****/

$doc->getRecolte()->add("appellation_2");
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
/*** NEW TEST ****/
$t->ok($obj_array_access = $doc['recolte']['appellation_ALSACEBLANC']['lieu']['cepage_PG']['detail'][0], 'ArrayAccess : can get value in the tree');
/*** NEW TEST ****/
$obj_array_access['denomination'] = 'test';
$t->is($obj_array_access->get('denomination'), 'test', 'ArrayAccess : can set value in the tree');
/*** NEW TEST ****/
$t->is($doc->getRecolte()->count(), 2, 'ArrayAccess : can count');

/*** NEW TEST ****/
$t->ok($doc->remove('recolte/appellation_ALSACEBLANC/lieu/cepage_PG/detail/0/superficie'), 'remove a field');
/*** NEW TEST ****/
 try{
   $t->ok(!$doc->get('recolte/appellation_ALSACEBLANC/lieu/cepage_PG/detail/0/superficie'), 'field removed');
}catch(Exception $e) {
  $t->pass('field removed');
 }

try{
  $doc->set('recolte/appellation_ALSACEBLANC/lieu/cepage_PG/detail/0/superficie', 150);
  $t->fail('cannot set a removed field ');
}catch(Exception $e) {
  $t->pass('cannot set a removed field ');
 }


$doc->set('recolte/appellation_ALSACEBLANC/lieu/cepage_PG/detail/0/volume', 69);

$detail = $doc->get('recolte/appellation_ALSACEBLANC/lieu/cepage_PG/detail/0');
$detail->add('superficie', 150);
$t->is($doc->get('recolte/appellation_ALSACEBLANC/lieu/cepage_PG/detail/0/superficie'), 150, 'superficie added');

/*** NEW TEST ****/
try {
$t->is($doc->get('/recolte/appellation_ALSACEBLANC/lieu/cepage_PG')->getCouchdbDocument(), $doc, 'can access couchdb doc from an sfcouchdbJson object');
}catch(Exception $e) {
  $t->fail('can access couchdb doc from an sfcouchdbJson object : '.$e);
 }
/*** NEW TEST ****/
$t->is($doc->get('/recolte/appellation_ALSACEBLANC/lieu/cepage_PG')->getHash(), '/recolte/appellation_ALSACEBLANC/lieu/cepage_PG', 'can access field hash from an sfcouchdbJson object');
$t->is($doc->get('/recolte/appellation_ALSACEBLANC/lieu/cepage_PG')->getParentHash(), '/recolte/appellation_ALSACEBLANC/lieu', 'can access field hash from an sfcouchdbJson object');
/*** NEW TEST ****/
$t->is($doc->get('/recolte/appellation_2')->getHash(), '/recolte/appellation_2', 'can access field hash from an sfcouchdbJson object issued of a collection');

/*** NEW TEST ****/
$t->is($doc->get('/recolte/appellation_ALSACEBLANC/lieu/cepage_PG/detail/0/superficie'), 150, 'can access to original superficie value');

$t->ok($detail2 = $doc->recolte->add('appellation_ALSACEBLANC')->add('lieu')->add('cepage_PG')->detail->add($detail2), 'add detail');

$detail2->setAppellation("ALSACEBLANC");
$detail2->setCepage("PG");
$detail2->setSuperficie(100);
$detail2->setVolume(20);
$detail2->setCaveParticuliere(10);
$acheteur = $detail2->getNegoces()->add();
$acheteur->setCvi("CVI_FICTIF");
$acheteur->setQuantiteVendue(10);

$t->is($doc->get('/recolte/appellation_ALSACEBLANC/lieu/cepage_PG/detail/1')->getHash(), '/recolte/appellation_ALSACEBLANC/lieu/cepage_PG/detail/1', 'can access field hash from a array collection');
$t->is($doc->get('/recolte/appellation_ALSACEBLANC/lieu/cepage_PG/detail/1/superficie'), 100, 'can access superficie value');
$t->is($doc->get('/recolte/appellation_ALSACEBLANC/lieu/cepage_PG/detail/0/superficie'), 150, 'can access to superficie other value');

/*** NEW TEST ****/
$nb = 0;
$cepage = $doc->get('/recolte/appellation_ALSACEBLANC/lieu/cepage_PG');
foreach($cepage->get('detail') as $k => $i) {
  $nb++;
}
$t->is($nb, 2, 'iterator on detail');

foreach($doc->get('/recolte/appellation_ALSACEBLANC/lieu/cepage_PG/detail') as $i => $class) {
  break;
}
$t->is(get_class($class), 'DRRecolteCepageDetail', 'Test the class name of a detail');
$data = $class->getData();
$t->ok($data->appellation, 'Test detail iterator data (appellation)');
$t->ok($data->superficie, 'Test detail iterator data (superficie)');

/*** NEW TEST ****/
try{
  $doc->update();
  $t->is($doc->get('/recolte/appellation_ALSACEBLANC/lieu/cepage_PG/total_volume'), 30, 'total volume for a cepage is automaticaly set');
  /*** NEW TEST ****/
$t->is($doc->get('/recolte/appellation_ALSACEBLANC/lieu/cepage_PG/total_superficie'), 250, 'total volume for a cepage is automaticaly set');
}catch(Exception $e) {
  $t->fail($e);
 }

$t->is($doc->get('recolte/appellation_ALSACEBLANC')->getTotalVolume(), 30, 'total volume accessible from appellation');
$t->is($doc->get('recolte/appellation_ALSACEBLANC')->getTotalSuperficie(), 250, 'total volume accessible from appellation');
//$t->ok($doc->get('recolte/appellation_ALSACEBLANC')->getTotalDPLC(), 'total DPLC');
//$t->ok($doc->get('recolte/appellation_ALSACEBLANC')->getTotalVolumeRevendique(), 'Total Volume revendiqué');


$t->isnt($doc->get('recolte/appellation_ALSACEBLANC/lieu/cepage_PG/detail/0/volume'), 0, 'volume not set to 0 before removeVolumes');
$doc->removeVolumes();
$doc->save();
$t->is($doc->get('recolte/appellation_ALSACEBLANC/lieu/cepage_PG/detail/0/volume'), 0, 'volume set to 0 after removeVolumes');

$t->is(sfCouchdbManager::getClient()->retrieveDocumentById('TESTCOUCHDB')->get('recolte/appellation_ALSACEBLANC/lieu/cepage_PG/detail/0/volume'), 0, 'volume set to 0 even after retrieve');

/*** NEW TEST ****/

$t->ok($doc->remove('/recolte/appellation_ALSACEBLANC/lieu/cepage_PG/detail/0'), 'remove a multifield');
/*** NEW TEST ****/
 try{
   $t->ok(!$doc->get('/recolte/appellation_ALSACEBLANC/lieu/cepage_PG/detail/0'), 'multifield removed');
}catch(Exception $e) {
  $t->pass('multifield removed');
 }

/*** NEW TEST ****/
$t->ok($doc->delete(), 'delete the document');
/*** NEW TEST ****/
try
{
  sfCouchdbManager::getClient()->getDoc('TESTCOUCHDB');
  $t->fail('cannot retrieve delete doc');
}catch(Exception $e) 
{
  $t->pass('cannot retrieve delete doc');
}


