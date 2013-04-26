<?php

class acVinTiersRouting {

    /**
     * Listens to the routing.load_configuration event.
     *
     * @param sfEvent An sfEvent instance
     * @static
     */
    static public function listenToRoutingLoadConfigurationEvent(sfEvent $event) {
        $r = $event->getSubject();

        $r->prependRoute('tiers_autocomplete_all', new sfRoute('/tiers/autocomplete/:interpro_id/tous',
                        array('module' => 'tiers_autocomplete',
                            'action' => 'all')));


        $r->prependRoute('tiers_autocomplete_byfamilles', new sfRoute('/tiers/autocomplete/:interpro_id/familles/:familles',
                        array('module' => 'tiers_autocomplete',
                            'action' => 'byFamilles')));


        
        $r->prependRoute('tiers_modification', new etablissementRoute('/tiers/:identifiant/modification',
                        array('module' => 'tiers',
                            'action' => 'modification'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'tiers',
                            'type' => 'object')));

        $r->prependRoute('tiers_visualisation', new etablissementRoute('/tiers/:identifiant/visualisation',
                        array('module' => 'tiers',
                            'action' => 'visualisation'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'tiers',
                            'type' => 'object')));                
    }

}