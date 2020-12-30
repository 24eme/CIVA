<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(63);

$configuration = ProjectConfiguration::getApplicationConfiguration('civa', 'test', true);
$databaseManager = new sfDatabaseManager($configuration);
$connection = $databaseManager->getDatabase()->getConnection();

$etablissement = EtablissementClient::getInstance()->find('ETABLISSEMENT-7523700100');
$negoces = ListAcheteursConfig::getNegoces();
foreach($negoces as $negoce) {
    break;
}
$appellationBlancKey = "appellation_ALSACEBLANC";
$produitBlancRIHash = "/recolte/certification/genre/appellation_ALSACEBLANC/mention/lieu/couleur/cepage_RI";
$produitBlancCHHash = "/recolte/certification/genre/appellation_ALSACEBLANC/mention/lieu/couleur/cepage_CH";
$appellationLieuxDitKey = "appellation_LIEUDIT";
$produitLieuxDitHash = "/recolte/certification/genre/appellation_LIEUDIT/mention/lieu/couleurBlanc/cepage_CH";

$dr = DRClient::getInstance()->find("DR-".$etablissement->identifiant."-2020", acCouchdbClient::HYDRATE_JSON);
DRClient::getInstance()->deleteDoc($dr);

$dr = DRClient::getInstance()->createDeclaration($etablissement, "2020");
$dr->save();

$t->ok($dr->_rev, "DR créé");

$t->comment("Séléction des appellations / acheteurs");

$dr->acheteurs->getNoeudAppellations()->getOrAdd($appellationBlancKey)->set("cave_particuliere", 1);
$dr->acheteurs->getNoeudAppellations()->getOrAdd($appellationBlancKey)->get("negoces")->add(null, $negoce["cvi"]);

$dr->acheteurs->getNoeudAppellations()->getOrAdd($appellationLieuxDitKey)->set("cave_particuliere", 1);
$dr->acheteurs->getNoeudAppellations()->getOrAdd($appellationLieuxDitKey)->get("negoces")->add(null, $negoce["cvi"]);

$dr->update(array('from_acheteurs'));
$dr->save();

$t->comment("Saisie d'une colonne AOC Alsace blanc");

$produitBlanc = $dr->getOrAdd($produitBlancRIHash);

$detail = $produitBlanc->detail->add();

$detail->superficie = 100;
$achat = $detail->negoces->add();
$achat->cvi = $negoce["cvi"];
$achat->quantite_vendue = 20;
$detail->cave_particuliere = 50;
$detail->lies = 2;
$detail->vci = 2;
$dr->update();

$t->is($produitBlanc->getTotalVolumeVendus(), 20, "Volume vendu");
$t->is($produitBlanc->getTotalCaveParticuliere(), 50, "Volume en cave particulière");
$t->is($produitBlanc->getConfig()->getRendementNoeud(), 65, "Rendement autorisé cépage");
$t->is($produitBlanc->getRendementMax(), 65, "Rendement autorisé avec vci");
$t->is($produitBlanc->getVolumeMaxRendement(), 65, "Volume maxiumum autorisé par le rendement");
$t->is($produitBlanc->getTotalVolume(), 70, "Volume total récolté");
$t->is($produitBlanc->getRendementRecoltant(), 68, "Rendement effectif du volume récolté (sans les lies)");
$t->is($produitBlanc->getVolumeRevendique(), 65, "Volume revendiqué");
$t->is($produitBlanc->getDplc(), 5, "DPLC");
$t->is($produitBlanc->getLies(), 2, "Lies");
$t->is($produitBlanc->getTotalVci(), 2, "VCI");
$t->is($produitBlanc->getUsagesIndustriels(), 3, "Usages industriels");
$t->ok(!$produitBlanc->canCalculVolumeRevendiqueSurPlace(), "Le volume revendiqué sur place n'est pas calculable");
$t->ok(!$produitBlanc->canCalculSuperficieSurPlace(), "La superficie sur place n'est pas calculable");

$produitBlancCH = $dr->getOrAdd($produitBlancCHHash);

$detail2 = $produitBlancCH->detail->add();

$detail2->superficie = 100;
$achat = $detail2->negoces->add();
$achat->cvi = $negoce["cvi"];
$achat->quantite_vendue = 70;
$detail2->cave_particuliere = 0;
$detail2->lies = 0;
$detail2->vci = 2;
$dr->update();

$t->is($produitBlancCH->getTotalSuperficieVendus(), 100, "Superficie vendu calculé automatiqument");
$t->is($produitBlancCH->getTotalDontDplcVendus(), 3, "DPLC vendu calculé automatiquement");
$t->is($produitBlancCH->getTotalDontVciVendus(), 2, "VCI vendu calculé automatiquement");

$detail3 = $produitBlancCH->detail->add();

$detail3->superficie = 100;
$detail3->cave_particuliere = 70;
$detail3->lies = 2;
$detail3->vci = 2;

$produitBlancCH->remove('acheteurs');
$produitBlancCH->add('acheteurs');

$dr->update();

$t->is($produitBlancCH->getTotalSuperficieVendus(), 100, "Superficie vendu calculé automatiqument");
$t->is($produitBlancCH->getTotalDontDplcVendus(), 0, "DPLC vendu non calculé automatiquement");
$t->is($produitBlancCH->getTotalDontVciVendus(), 2, "VCI vendu calculé automatiquement");

$detail3->superficie = 200;
$dr->update();

$t->is($produitBlancCH->getTotalSuperficieVendus(), 100, "Superficie vendu calculé automatiqument");
$t->is($produitBlancCH->getTotalDontDplcVendus(), 0, "DPLC vendu calculé automatiquement");
$t->is($produitBlancCH->getTotalDontVciVendus(), 2, "VCI vendu calculé automatiquement");

$t->comment("Saisie d'une colonne AOC Alsace Lieux-dit");

$detail = $dr->getOrAdd($produitLieuxDitHash)->detail->add();
$produitLieuxDit = $detail->getCouleur();

$detail->lieu = "COLMAR";
$detail->superficie = 100;
$achat = $detail->negoces->add();
$achat->cvi = $negoce["cvi"];
$achat->quantite_vendue = 20;
$detail->cave_particuliere = 53;
$detail->lies = 3;
$dr->update();

$t->is($produitLieuxDit->getTotalVolumeVendus(), 20, "Volume vendu");
$t->is($produitLieuxDit->getTotalCaveParticuliere(), 53, "Volume en cave particulière");
$t->is($produitLieuxDit->getConfig()->getRendementNoeud(), 55, "Rendement autorisé cépage");
$t->is($produitLieuxDit->getRendementMax(), 55, "Rendement autorisé");
$t->is($produitLieuxDit->getVolumeMaxRendement(), 55, "Volume maxiumum autorisé par le rendement");
$t->is($produitLieuxDit->getTotalVolume(), 73, "Volume total récolté");
$t->is($produitLieuxDit->getRendementRecoltant(), 70, "Rendement effectif du volume récolté (sans les lies)");
$t->is($produitLieuxDit->getVolumeRevendique(), 55, "Volume revendiqué");
$t->is($produitLieuxDit->getDplc(), 18, "DPLC");
$t->is($produitLieuxDit->getLies(), 3, "Lies");
$t->is($produitLieuxDit->getUsagesIndustriels(), 18, "Usages industriels");
$t->ok(!$produitLieuxDit->canCalculVolumeRevendiqueSurPlace(), "Le volume revendiqué sur place n'est pas calculable");
$t->ok(!$produitLieuxDit->canCalculSuperficieSurPlace(), "La superficie sur place n'est pas calculable");

$dr->save();

$t->comment("Récapitulatif des ventes AOC Alsace blanc");

$t->ok($produitBlanc->hasRecapitulatif(), "Le cépage nécessite un recap de vente");
$t->ok($produitBlanc->hasRecapitulatifVente(), "Le cépage nécessite un recap de vente et le noeud acheteur est présent");

$achat = $produitBlanc->acheteurs->negoces->get($negoce["cvi"]);
$achat->dontvci = 1;
$achat->dontdplc = 1;
$achat->superficie = 10;

$dr->update();

$t->ok($produitBlanc->canCalculVolumeRevendiqueSurPlace(), "Le volume revendiqué sur place est calculable");
$t->ok($produitBlanc->canCalculSuperficieSurPlace(), "La superficie sur place est calculable");
$t->is($produitBlanc->getSuperficieCaveParticuliere(), 90, "Superficie sur place");
$t->is($produitBlanc->getDplcCaveParticuliere(), 0, "DPLC sur place");
$t->is($produitBlanc->getVolumeRevendiqueCaveParticuliere(), 47, "Volume revendique sur place");
$t->is($produitBlanc->getTotalSuperficieVendus(), 10, "Superficie vendus");
$t->is($produitBlanc->getTotalDontDplcVendus(), 1, "DPLC vendus");

$t->comment("Récapitulatif des ventes AOC Alsace Lieux-dit");

$t->ok($produitLieuxDit->hasRecapitulatif(), "Le cépage nécessite un recap de vente");
$t->ok($produitLieuxDit->hasRecapitulatifVente(), "Le cépage nécessite un recap de vente et le noeud acheteur est présent");

$achat = $produitLieuxDit->acheteurs->negoces->get($negoce["cvi"]);
$achat->dontdplc = 1;
$achat->superficie = 10;

$dr->update();

$t->ok($produitLieuxDit->canCalculVolumeRevendiqueSurPlace(), "Le volume revendiqué sur place est calculable");
$t->ok($produitLieuxDit->canCalculSuperficieSurPlace(), "La superficie sur place est calculable");
$t->is($produitLieuxDit->getSuperficieCaveParticuliere(), 90, "Superficie sur place");
$t->is($produitLieuxDit->getDplcCaveParticuliere(), 14, "DPLC sur place");
$t->is($produitLieuxDit->getVolumeRevendiqueCaveParticuliere(), 36, "Volume revendiqué sur place");
$t->is($produitLieuxDit->getTotalSuperficieVendus(), 10, "Superficie vendus");
$t->is($produitLieuxDit->getTotalDontDplcVendus(), 1, "DPLC vendus");

$dr->save();

$t->comment("PDF");

$achatCouleur = $produitBlanc->getCouleur()->acheteurs->negoces->get($negoce["cvi"]);
$t->is($achatCouleur->superficie, 110, "Superficie acheteur au niveau couleur");
$t->is($achatCouleur->volume, 90, "Volume acheteur au niveau couleur");
$t->is($achatCouleur->dontvci, 3, "Dont vci au niveau couleur");
$t->is($achatCouleur->dontdplc, 1, "Dont dplc au niveau couleur");

$achatLieu = $produitBlanc->getLieu()->acheteurs->negoces->get($negoce["cvi"]);
$t->is($achatLieu->superficie, 110, "Superficie acheteur au niveau lieu");
$t->is($achatLieu->volume, 90, "Volume acheteur au niveau lieu");
$t->is($achatLieu->dontvci, 3, "Dont vci au niveau lieu");
$t->is($achatLieu->dontdplc, 1, "Dont dplc au niveau lieu");

$t->comment("XML douane");
