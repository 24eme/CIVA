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
        
        
        $r->prependRoute('ds_etape_redirect', new DSRoute('/ds/:id/ds-etape-redirect', array('module' => 'ds',
                        'action' => 'redirectEtape'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'DS',
                            'type' => 'object') ));

        $r->prependRoute('ds_exploitation', new TiersRoute('/ds/:cvi/exploitation', array('module' => 'ds',
                    'action' => 'exploitation'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'Tiers',
                            'type' => 'object')));
        
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

        $r->prependRoute('ds_ajout_lieu', new DSNoeudRouteCiva('/ds/:id/ajout-lieu/:hash', array('module' => 'ds',
                        'action' => 'ajoutLieu'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'DS',
                            'type' => 'object') ));      

        $r->prependRoute('ds_ajout_produit', new DSNoeudRouteCiva('/ds/:id/ajout-produit/:hash', array('module' => 'ds',
                        'action' => 'ajoutProduit'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'DS',
                            'type' => 'object') ));

        $r->prependRoute('ds_edition_operateur', new DSNoeudRouteCiva('/ds/:id/edition/:hash', array('module' => 'ds',
                        'action' => 'stock', 'hash' => null),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'DS',
                            'type' => 'object')));
       
        $r->prependRoute('ds_recapitulatif_lieu_stockage', new DSRoute('/ds/:id/recapitulatif-lieu-stockage', array('module' => 'ds',
                        'action' => 'recapitulatifLieuStockage'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'DS',
                            'type' => 'object') ));
        
        $r->prependRoute('ds_autre', new TiersRoute('/ds/:cvi/autre', array('module' => 'ds',
                        'action' => 'autre'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'Tiers',
                            'type' => 'object') ));
        
        $r->prependRoute('ds_validation', new TiersRoute('/ds/:cvi/validation', array('module' => 'ds',
                'action' => 'validation'),
                array('sf_method' => array('get', 'post')),
                array('model' => 'Tiers',
                    'type' => 'object') ));

        $r->prependRoute('ds_export_pdf', new DSRoute('/ds/:id/pdf', array('module' => 'ds_export',
                'action' => 'PDF'),
                array('sf_method' => array('get')),
                array('model' => 'DS',
                    'type' => 'object') ));
        
        
        
        
    }
}