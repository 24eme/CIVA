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

$browser->info("Tiers Login (7523700100")
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

//$browser->with('response')->debug();