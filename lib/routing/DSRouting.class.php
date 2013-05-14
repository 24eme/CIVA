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

        $r->prependRoute('ds_lieux_stockage', new TiersRoute('/ds/:cvi/lieux-stockage', array('module' => 'ds',
                    'action' => 'lieuxStockage'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'Tiers',
                            'type' => 'object')));
        
        
        $r->prependRoute('ds_tiers', new TiersRoute('/ds/:cvi', array('module' => 'ds',
                    'action' => 'monEspace'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'Tiers',
                            'type' => 'object')));
        

    

        $r->prependRoute('ds_edition_operateur', new DSRoute('/ds/:id/edition/:appellation', array('module' => 'ds',
                        'action' => 'stock'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'DS',
                            'type' => 'object') ));
        
        $r->prependRoute('ds_recapitulatif_lieu_stockage', new DSRoute('/ds/:id/recapitulatif-lieu-stockage/:lieu_stockage', array('module' => 'ds',
                        'action' => 'recapitulatifLieuStockage'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'DS',
                            'type' => 'object') ));
        
        
        
    }
}