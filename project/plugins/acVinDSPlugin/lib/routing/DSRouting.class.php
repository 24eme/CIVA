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
        $r->prependRoute('ds_init', new EtablissementRoute('/ds/:identifiant/initialisation/:type', array('module' => 'ds',
                        'action' => 'init'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'Etablissement',
                            'type' => 'object') ));

        $r->prependRoute('ds_etape_redirect', new DSRoute('/ds/:id/ds-etape-redirect', array('module' => 'ds',
                        'action' => 'redirectEtape'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'DS',
                            'type' => 'object') ));

        $r->prependRoute('ds_exploitation', new DSRoute('/ds/:id/exploitation', array('module' => 'ds',
                    'action' => 'exploitation'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'DS',
                            'type' => 'object')));

        $r->prependRoute('ds_lieux_stockage', new DSRoute('/ds/:id/lieux-stockage', array('module' => 'ds',
                    'action' => 'lieuxStockage'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'DS',
                            'type' => 'object')));

        $r->prependRoute('ds_ajout_lieux_stockage', new DSRoute('/ds/:id/lieux-stockage-ajout', array('module' => 'ds',
                    'action' => 'lieuxStockageAjout'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'DS',
                            'type' => 'object')));

        $r->prependRoute('ds_ajout_lieu', new DSNoeudRouteCiva('/ds/:id/ajout-lieu/:hash', array('module' => 'ds',
                        'action' => 'ajoutLieu'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'DS',
                            'type' => 'object') ));

        $r->prependRoute('ds_ajout_appellation', new DSRoute('/ds/:id/ajout-appellation', array('module' => 'ds',
                'action' => 'ajoutAppellation'),
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

        $r->prependRoute('ds_autre', new DSRoute('/ds/:id/autre', array('module' => 'ds',
                        'action' => 'autre'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'DS',
                            'type' => 'object') ));

        $r->prependRoute('ds_validation', new DSRoute('/ds/:id/validation', array('module' => 'ds',
                'action' => 'validation'),
                array('sf_method' => array('get', 'post')),
                array('model' => 'DS',
                    'type' => 'object') ));

         $r->prependRoute('ds_confirmation', new DSRoute('/ds/:id/confirmation', array('module' => 'ds',
                        'action' => 'confirmation'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'DS',
                            'type' => 'object') ));

        $r->prependRoute('ds_visualisation', new DSRoute('/ds/:id/visualisation', array('module' => 'ds',
                'action' => 'visualisation'),
                array('sf_method' => array('get', 'post')),
                array('model' => 'DS',
                    'type' => 'object') ));

        $r->prependRoute('ds_invalider_civa', new DSRoute('/ds/:id/invalider-civa', array('module' => 'ds',
                'action' => 'invaliderCiva'),
                array('sf_method' => array('get')),
                array('model' => 'DS',
                    'type' => 'object') ));

        $r->prependRoute('ds_invalider_recoltant', new DSRoute('/ds/:id/invalider-recoltant', array('module' => 'ds',
                'action' => 'invaliderRecoltant'),
                array('sf_method' => array('get')),
                array('model' => 'DS',
                    'type' => 'object') ));

        $r->prependRoute('ds_export_pdf', new DSRoute('/ds/:id/pdf', array('module' => 'ds_export',
                'action' => 'PDF'),
                array('sf_method' => array('get')),
                array('model' => 'DS',
                    'type' => 'object') ));

        $r->prependRoute('ds_export_pdf_empty', new sfRoute('/ds/pdf-vide/:type', array('module' => 'ds_export',
                'action' => 'PDFEmpty'),
                array('sf_method' => array('get'))));

        $r->prependRoute('ds_export_csv_en_cours', new sfRoute('/ds/csv/en_cours', array('module' => 'ds_export',
                'action' => 'csvEnCours'),
                array('sf_method' => array('get'))));

        $r->prependRoute('ds_send_email_pdf', new DSRoute('/ds/:id/envoi-email', array('module' => 'ds',
                'action' => 'sendEmail'),
                array('sf_method' => array('get')),
                array('model' => 'DS',
                    'type' => 'object') ));

        $r->prependRoute('telecharger_la_notice_ds', new sfRoute('/ds/telecharger-la-notice-ds/:type', array('module' => 'ds',
                'action' => 'downloadNotice')));

        $r->prependRoute('telecharger_la_dai', new sfRoute('/ds/telecharger-la-dai', array('module' => 'ds',
                'action' => 'downloadDai')));

        $r->prependRoute('ds_feed_back', new sfRoute('/ds/retour-experience/:type', array('module' => 'ds',
                'action' => 'feedBack'),
                array('sf_method' => array('get'))));

        $r->prependRoute('ds_feed_back_confirmation', new sfRoute('/ds/retour-experience-confirmation/:type', array('module' => 'ds',
                'action' => 'feedBackConfirmation'),
                array('sf_method' => array('get'))));
    }
}
