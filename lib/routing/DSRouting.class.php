<?php

class DSRouting {

    /**
     * Listens to the routing.load_configuration event.
     *
     * @param sfEvent An sfEvent instance
     * @static
     */
    static public function listenToRoutingLoadConfigurationEvent(sfEvent $event) {

        $r = $event->getSubject();
        $r->prependRoute('ds', new sfRoute('/ds', array('module' => 'ds',
                    'action' => 'index')));

        $r->prependRoute('ds_tiers', new TiersRoute('/ds/:cvi', array('module' => 'ds',
                    'action' => 'monEspace'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'Tiers',
                            'type' => 'object')));
        

        $r->prependRoute('ds_lieux_stockage', new TiersRoute('/ds/lieux-stockage/:cvi', array('module' => 'ds',
                    'action' => 'lieuxStockage'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'Tiers',
                            'type' => 'object')));

//        $r->prependRoute('ds_generation', new sfRoute('/ds/generation', array('module' => 'ds', 
//                                                                              'action' => 'generation')));   
//        
//        $r->prependRoute('ds_historique_generation', new sfRoute('/ds/generation/historique', array('module' => 'ds', 
//										      'action' => 'historiqueGeneration'))); 
//        
//        $r->prependRoute('ds_generation_operateur', new DSRoute('/ds/:identifiant/:periode/generation', array('module' => 'ds',
//                    'action' => 'generationOperateur'),
//                        array('sf_method' => array('get', 'post')),
//                        array('model' => 'DS',
//                            'type' => 'object')));
//        

        $r->prependRoute('ds_edition_operateur', new DSRoute('/ds/:identifiant/:periode/:lieu_stockage/edition', array('module' => 'ds',
                        'action' => 'stock'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'DS',
                            'type' => 'object') ));
//        
//        
//        $r->prependRoute('ds_edition_operateur_addProduit', new DSRoute('/ds/:identifiant/:periode/edition/addProduit', array('module' => 'ds',
//                    'action' => 'editionDSAddProduit'),
//                    array('sf_method' => array('get', 'post')),
//                    array('model' => 'DS',
//                        'type' => 'object') ));
//        
//        $r->prependRoute('ds_edition_operateur_validation_visualisation', new DSRoute('/ds/:identifiant/:periode/visualisation', array('module' => 'ds',
//            'action' => 'editionDSValidationVisualisation'),
//            array('sf_method' => array('get', 'post')),
//            array('model' => 'DS',
//                'type' => 'object') ));
//        
//        $r->prependRoute('ds_pdf', new DSRoute('/ds/:identifiant/:periode/pdf', array('module' => 'ds', 
//											        'action' => 'latex'),
//									 array('sf_method' => array('get','post')),
//									 array('model' => 'DS',
//									       'type' => 'object')
//									 ));
        
    }
}