<?php

require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once dirname(__FILE__).'/00_Init.php';

$etablissementsProducteur = EtablissementClient::getInstance()->findByFamille('PRODUCTEUR');
$producteur1 = EtablissementClient::getInstance()->find(current($etablissementsProducteur)->id);
$negociant1 = current(ListAcheteursConfig::getNegoces());

$dr = DRClient::getInstance()->createDeclaration($producteur1, "2021");
$produit1Hash = "/recolte/certification/genre/appellation_ALSACEBLANC/mention/lieu/couleur/cepage_GW";
$produit2Hash = "/recolte/certification/genre/appellation_LIEUDIT/mention/lieu/couleurBlanc/cepage_CH";
$produit3Hash = "/recolte/certification/genre/appellation_ALSACEBLANC/mention/lieu/couleur/cepage_MU";
$denominations = array(null, "VIELLES VIGNES");

foreach ([$produit1Hash, $produit2Hash, $produit3Hash] as $p) {
    foreach($denominations as $denomination) {
        $produit = $dr->getOrAdd($p);
        $detail = $produit->detail->add();
        $detail->superficie = 100;
        $detail->volume = 1000;
        $detail->denomination = $denomination;

        $achat = $detail->negoces->add();
        $achat->cvi = $negociant1['cvi'];
        $achat->quantite_vendue = 50;

        $dr->acheteurs->getNoeudAppellations()->getOrAdd(
            $produit->getAppellation()->getKey()
        )->set('cave_particuliere', 1);

        $dr->acheteurs->getNoeudAppellations()->getOrAdd(
            $produit->getAppellation()->getKey()
        )->get('negoces')->add(null, $negoce['cvi']);
    }
}

$dr->validate();
$dr->save();

$t = new lime_test(4);

$sv = SVClient::getInstance()->createFromDR($negociant1['cvi'], "2021");
$sv->save();

$t->is($sv->_id, 'SV12-'.$negociant1['cvi'].'-2021', 'ID du document');
$t->is(count($sv->apporteurs->get($producteur1->cvi)->toArray(true, false)), 3, '3 produits importé depuis la DR');
$t->is(count($sv->apporteurs->get($producteur1->cvi)->getFirst()->toArray(true, false)), 2, '2 détails dans le premier produit');
$t->is($sv->apporteurs->get($producteur1->cvi)->getFirst()->getLast()->denomination_complementaire, "VIELLES VIGNES", "La dénomination complémentaire du 2ème produit est VIELLES VIGNES");

