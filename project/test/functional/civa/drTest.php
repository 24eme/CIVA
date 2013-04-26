<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new sfTestFunctional(new sfBrowser());

$browser->info("Compte login (Admin)")
        ->get('/')
        ->followRedirect()
        ->followRedirect()
        ->with('request')
            ->begin()
                ->isParameter('module', 'admin')
                ->isParameter('action', 'index')
            ->end()
        ->with('response')
            ->begin()->isStatusCode(200)->end();

$browser->info("Tiers Login (7523700100) ")
        ->post('/admin', array('admin' => array('login' => '7523700100')))
        ->followRedirect()
        ->followRedirect()
        ->with('request')
            ->begin()
                ->isParameter('module', 'tiers')
                ->isParameter('action', 'monEspaceCiva')
            ->end()
        ->with('response')
            ->begin()
                ->isStatusCode(200)
            ->end();

$browser->info("exploitation_administratif (7523700100) ")
        ->post('/exploitation_administratif')
        ->followRedirect()
        ->with('request')
            ->begin()
                ->isParameter('module', 'tiers')
                ->isParameter('action', 'exploitationAdministratif')
            ->end()
        ->with('response')
            ->begin()
                ->isStatusCode(200)
            ->end();


$browser->info("exploitation_administratif (7523700100) ")
        ->post('/exploitation_administratif')
        
        ->with('request')
            ->begin()
                ->isParameter('module', 'tiers')
                ->isParameter('action', 'exploitationAdministratif')
            ->end()
        ->with('response')
            ->begin()
                ->isStatusCode(200)
            ->end();

$browser->info("exploitation_acheteurs (7523700100) ")
        ->get('/exploitation_acheteurs')
        ->with('request')
            ->begin()
                ->isParameter('module', 'acheteur')
                ->isParameter('action', 'exploitationAcheteurs')
            ->end()
        ->with('response')
            ->begin()
                ->isStatusCode(200)
            ->end();

 /*
  * /recolte/:appellation_lieu/:couleur_cepage
  */


$appelations = ExploitationAcheteursForm::getListeAppellations();

$browser->info("get exploitation_acheteurs (7523700100) ")
        ->get('/exploitation_acheteurs');

$dom = new DOMDocument('1.0', $browser->getResponse()->getCharset());
$dom->loadHTML($browser->getResponse()->getContent());
$domCssSelector = new sfDomCssSelector($dom);
$token = $domCssSelector->matchSingle('input[name="exploitation_acheteurs[_csrf_token]"]')->getNode()->getAttribute('value');
$browser->post('/exploitation_acheteurs', array('exploitation_acheteurs' => 
                                                 array('_csrf_token' => $token,
                                                       'cave_particuliere' => array('appellation_ALSACEBLANC' => 'on')),
                                                'boutons' => array('next' => '1')))
        ->with('request')
            ->begin()
                ->isParameter('module', 'acheteur')
                ->isParameter('action', 'exploitationAcheteurs')               
            ->end()
        ->with('response');

$browser->followRedirect()
        
         ->with('request')
            ->begin()
                ->isParameter('module', 'acheteur')
                ->isParameter('action', 'exploitationLieu')               
            ->end()
        ->with('response');

$browser->followRedirect()
          ->with('request')
            ->begin()
                ->isParameter('module', 'recolte')
                ->isParameter('action', 'recolte')                     
            ->end()
        ->with('response');
        
$browser->followRedirect()
        
        ->with('request')
            ->begin()
                ->isParameter('module', 'recolte')
                ->isParameter('action', 'add')
                ->isParameter('appellation_lieu', 'ALSACEBLANC')
                ->isParameter('couleur_cepage', 'CH')
            ->end()
        ->with('response')
      
            ->begin()
                ->isStatusCode(200)
            ->end();


$browser->post('/exploitation_acheteurs', array('exploitation_acheteurs' => 
                                                 array('_csrf_token' => $token,
                                                       'cave_particuliere' => array('appellation_LIEUDIT' => 'on')),
                                                'boutons' => array('next' => '1')))
        ->with('request')
            ->begin()
                ->isParameter('module', 'acheteur')
                ->isParameter('action', 'exploitationAcheteurs')               
            ->end()
        ->with('response');

$browser->followRedirect()
        
         ->with('request')
            ->begin()
                ->isParameter('module', 'acheteur')
                ->isParameter('action', 'exploitationLieu')               
            ->end()
        ->with('response');

$browser->followRedirect()
          ->with('request')
            ->begin()
                ->isParameter('module', 'recolte')
                ->isParameter('action', 'recolte')                     
            ->end()
        ->with('response');
        
$browser->followRedirect()
        
        ->with('request')
            ->begin()
                ->isParameter('module', 'recolte')
                ->isParameter('action', 'add')
                ->isParameter('appellation_lieu', 'LIEUDIT')
                ->isParameter('couleur_cepage', 'Blanc-CH')
            ->end()
        ->with('response')
      
            ->begin()
                ->isStatusCode(200)
            ->end();


$browser->post('/exploitation_acheteurs', array('exploitation_acheteurs' => 
                                                 array('_csrf_token' => $token,
                                                       'cave_particuliere' => array('appellation_COMMUNALE' => 'on')),
                                                'boutons' => array('next' => '1')))
        ->with('request')
            ->begin()
                ->isParameter('module', 'acheteur')
                ->isParameter('action', 'exploitationAcheteurs')               
            ->end()
        ->with('response');

$browser->followRedirect()
        
         ->with('request')
            ->begin()
                ->isParameter('module', 'acheteur')
                ->isParameter('action', 'exploitationLieu')               
            ->end()
        ->with('response')
            ->begin()
                ->isStatusCode(200)
            ->end();

$browser->post('/exploitation_acheteurs', array('exploitation_acheteurs' => 
                                                 array('_csrf_token' => $token,
                                                       'cave_particuliere' => array('appellation_GRDCRU' => 'on')),
                                                'boutons' => array('next' => '1')))
        ->with('request')
            ->begin()
                ->isParameter('module', 'acheteur')
                ->isParameter('action', 'exploitationAcheteurs')               
            ->end()
        ->with('response');

$browser->followRedirect()
        
         ->with('request')
            ->begin()
                ->isParameter('module', 'acheteur')
                ->isParameter('action', 'exploitationLieu')               
            ->end()
        ->with('response')
            ->begin()
                ->isStatusCode(200)
            ->end();

$browser->post('/exploitation_acheteurs', array('exploitation_acheteurs' => 
                                                 array('_csrf_token' => $token,
                                                       'cave_particuliere' => array('appellation_PINOTNOIR' => 'on')),
                                                'boutons' => array('next' => '1')))
        ->with('request')
            ->begin()
                ->isParameter('module', 'acheteur')
                ->isParameter('action', 'exploitationAcheteurs')               
            ->end()
        ->with('response');

$browser->followRedirect()
        
         ->with('request')
            ->begin()
                ->isParameter('module', 'acheteur')
                ->isParameter('action', 'exploitationLieu')               
            ->end()
        ->with('response');

$browser->followRedirect()
          ->with('request')
            ->begin()
                ->isParameter('module', 'recolte')
                ->isParameter('action', 'recolte')                     
            ->end()
        ->with('response');
        
$browser->followRedirect()
        
        ->with('request')
            ->begin()
                ->isParameter('module', 'recolte')
                ->isParameter('action', 'add')
                ->isParameter('appellation_lieu', 'PINOTNOIR')
                ->isParameter('couleur_cepage', 'PN')
            ->end()
        ->with('response')
      
            ->begin()
                ->isStatusCode(200)
            ->end();

$browser->post('/exploitation_acheteurs', array('exploitation_acheteurs' => 
                                                 array('_csrf_token' => $token,
                                                       'cave_particuliere' => array('appellation_PINOTNOIRROUGE' => 'on')),
                                                'boutons' => array('next' => '1')))
        ->with('request')
            ->begin()
                ->isParameter('module', 'acheteur')
                ->isParameter('action', 'exploitationAcheteurs')               
            ->end()
        ->with('response');

$browser->followRedirect()
        
         ->with('request')
            ->begin()
                ->isParameter('module', 'acheteur')
                ->isParameter('action', 'exploitationLieu')               
            ->end()
        ->with('response');

$browser->followRedirect()
          ->with('request')
            ->begin()
                ->isParameter('module', 'recolte')
                ->isParameter('action', 'recolte')                     
            ->end()
        ->with('response');
        
$browser->followRedirect()
        
        ->with('request')
            ->begin()
                ->isParameter('module', 'recolte')
                ->isParameter('action', 'add')
                ->isParameter('appellation_lieu', 'PINOTNOIRROUGE')
                ->isParameter('couleur_cepage', 'PR')
            ->end()
        ->with('response')
      
            ->begin()
                ->isStatusCode(200)
            ->end();

$browser->post('/exploitation_acheteurs', array('exploitation_acheteurs' => 
                                                 array('_csrf_token' => $token,
                                                       'cave_particuliere' => array('appellation_CREMANT' => 'on')),
                                                'boutons' => array('next' => '1')))
        ->with('request')
            ->begin()
                ->isParameter('module', 'acheteur')
                ->isParameter('action', 'exploitationAcheteurs')               
            ->end()
        ->with('response');

$browser->followRedirect()
        
         ->with('request')
            ->begin()
                ->isParameter('module', 'acheteur')
                ->isParameter('action', 'exploitationLieu')               
            ->end()
        ->with('response');

$browser->followRedirect()
          ->with('request')
            ->begin()
                ->isParameter('module', 'recolte')
                ->isParameter('action', 'recolte')                     
            ->end()
        ->with('response');
        
$browser->followRedirect()
        
        ->with('request')
            ->begin()
                ->isParameter('module', 'recolte')
                ->isParameter('action', 'add')
                ->isParameter('appellation_lieu', 'CREMANT')
                ->isParameter('couleur_cepage', 'PB')
            ->end()
        ->with('response')
      
            ->begin()
                ->isStatusCode(200)
            ->end();


$browser->post('/exploitation_acheteurs', array('exploitation_acheteurs' => 
                                                 array('_csrf_token' => $token,
                                                       'cave_particuliere' => array('appellation_VINTABLE' => 'on')),
                                                'boutons' => array('next' => '1')))
        ->with('request')
            ->begin()
                ->isParameter('module', 'acheteur')
                ->isParameter('action', 'exploitationAcheteurs')               
            ->end()
        ->with('response');

$browser->followRedirect()
        
         ->with('request')
            ->begin()
                ->isParameter('module', 'acheteur')
                ->isParameter('action', 'exploitationLieu')               
            ->end()
        ->with('response');

$browser->followRedirect()
          ->with('request')
            ->begin()
                ->isParameter('module', 'recolte')
                ->isParameter('action', 'recolte')                     
            ->end()
        ->with('response');
        
$browser->followRedirect()
        
        ->with('request')
            ->begin()
                ->isParameter('module', 'recolte')
                ->isParameter('action', 'add')
                ->isParameter('appellation_lieu', 'VINTABLE')
                ->isParameter('couleur_cepage', 'BL')
            ->end()
        ->with('response')
      
            ->begin()
                ->isStatusCode(200)
            ->end();

$browser->post('/recapitulatif/ALSACEBLANC', array('redirect' => '1'))
        ->with('request')
            ->begin()
                ->isParameter('module', 'recolte')
                ->isParameter('action', 'recapitulatif')                     
            ->end()
        ->with('response');


$browser->followRedirect()->with('request')
            ->begin()
                ->isParameter('module', 'declaration')
                ->isParameter('action', 'exploitationAutres')
            ->end()
        ->with('response')
            ->begin()
                ->isStatusCode(200)
            ->end();



//$browser->with('response')->debug();