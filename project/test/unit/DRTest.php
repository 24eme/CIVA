<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(32);

$configuration = ProjectConfiguration::getApplicationConfiguration('civa', 'test', true);
$databaseManager = new sfDatabaseManager($configuration);
$connection = $databaseManager->getDatabase()->getConnection();

$etablissement = EtablissementClient::getInstance()->find('ETABLISSEMENT-7523700100');
$negoces = ListAcheteursConfig::getNegoces();
foreach($negoces as $negoce) {
    break;
}
$appellationKey = "appellation_ALSACEBLANC";
$produitHash = "/recolte/certification/genre/appellation_ALSACEBLANC/mention/lieu/couleur/cepage_RI";

$dr = DRClient::getInstance()->find("DR-".$etablissement->identifiant."-2020", acCouchdbClient::HYDRATE_JSON);
DRClient::getInstance()->deleteDoc($dr);

$dr = DRClient::getInstance()->createDeclaration($etablissement, "2020");
$dr->save();

$t->ok($dr->_rev, "DR créé");

$t->comment("Séléction des appellations / acheteurs");

$dr->acheteurs->getNoeudAppellations()->getOrAdd($appellationKey)->set("cave_particuliere", 1);
$dr->acheteurs->getNoeudAppellations()->getOrAdd($appellationKey)->get("negoces")->add(null, $negoce["cvi"]);
$dr->update(array('from_acheteurs'));
$dr->save();

$t->comment("Saisie des colonnes");

$produit = $dr->getOrAdd($produitHash);

$detail = $produit->detail->add();

$detail->superficie = 100;
$achat = $detail->negoces->add();
$achat->cvi = $negoce["cvi"];
$achat->quantite_vendue = 20;
$detail->cave_particuliere = 50;
$detail->lies = 3;
$detail->vci = 2;

$dr->update();

$t->is($produit->getTotalVolumeVendus(), 20, "Volume vendu");
$t->is($produit->getTotalCaveParticuliere(), 50, "Volume en cave particulière");
$t->is($produit->getConfig()->getRendementNoeud(), 65, "Rendement autorisé cépage");
$t->is($produit->getRendementMax(), 67, "Rendement autorisé avec vci");
$t->is($produit->getVolumeMaxRendement(), 65, "Volume maxiumum autorisé par le rendement");
$t->is($produit->getTotalVolume(), 70, "Volume total récolté");
$t->is($produit->getRendementRecoltant(), 67, "Rendement effectif du volume récolté (sans les lies)");
$t->is($produit->getVolumeRevendique(), 65, "Volume revendiqué");
$t->is($produit->getDplc(), 5, "DPLC");
$t->is($produit->getLies(), 3, "Lies");
$t->is($produit->getTotalVci(), 2, "VCI");
$t->is($produit->getUsagesIndustriels(), 3, "Usages industriels");
$t->ok(!$produit->canCalculVolumeRevendiqueSurPlace(), "Le volume revendiqué sur place n'est pas calculable");
$t->ok(!$produit->canCalculSuperficieSurPlace(), "La superficie sur place n'est pas calculable");
$t->is($produit->getDplcCaveParticuliere(), 0, "Le DPLC en cave particulière est de 0 car on ne connait pas la répartion");
$t->is($produit->getTotalDontDplcVendus(), 0, "Le DPLC vendu est de 0 car on ne connait pas la répartion");
$t->is($produit->getTotalSuperficieVendus(), 0, "La superficie vendu est de 0 car on ne connait pas la répartion");
$t->is($produit->getTotalDontVciVendus(), 0, "Le vci vendu est de 0 car on ne connait pas la répartion");

$dr->save();

$t->comment("Récapitulatif des ventes");

$t->ok($produit->hasRecapitulatif(), "Le cépage nécessite un recap de vente");
$t->ok($produit->hasRecapitulatifVente(), "Le cépage nécessite un recap de vente et le noeud acheteur est présent");

$achat = $produit->acheteurs->negoces->get($negoce["cvi"]);
$achat->dontvci = 1;
$achat->dontdplc = 1;
$achat->superficie = 10;

$t->ok($produit->canCalculVolumeRevendiqueSurPlace(), "Le volume revendiqué sur place est calculable");
$t->ok($produit->canCalculSuperficieSurPlace(), "La superficie sur place est calculable");
$t->is($produit->getVolumeRevendiqueCaveParticuliere(), 47, "Volume revendique sur place");

$dr->update();
$dr->save();

$t->comment("PDF");

$achatCouleur = $produit->getCouleur()->acheteurs->negoces->get($negoce["cvi"]);
$t->is($achatCouleur->superficie, 10, "Superficie acheteur au niveau couleur");
$t->is($achatCouleur->volume, 20, "Volume acheteur au niveau couleur");
$t->is($achatCouleur->dontvci, 1, "Dont vci au niveau couleur");
$t->is($achatCouleur->dontdplc, 1, "Dont dplc au niveau couleur");

$achatLieu = $produit->getLieu()->acheteurs->negoces->get($negoce["cvi"]);
$t->is($achatLieu->superficie, 10, "Superficie acheteur au niveau lieu");
$t->is($achatLieu->volume, 20, "Volume acheteur au niveau lieu");
$t->is($achatLieu->dontvci, 1, "Dont vci au niveau lieu");
$t->is($achatLieu->dontdplc, 1, "Dont dplc au niveau lieu");
