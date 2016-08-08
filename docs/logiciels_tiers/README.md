#Spécifications techniques de l'implémentation du format de DRM attendues sur le portail du CIVA

La spécification complète du format d'import attendue est détaillée ici : [Spécification générique DRM logiciels tiers](https://github.com/24eme/mutualisation-douane/blob/master/logiciels-tiers/edi/speficication_technique.md). Cette documentation est commune pour les portails déclaratifs du CIVA, du CIVP, d'Interloire, d'InterRhone, d'IVBD, d'IVSO et d'IVSE.

Cette page apporte un éclairage CIVA à la documentation générique. Elle permet d'accéder à la liste des produits CIVA (et la manière de les déclarer) ainsi que les mouvements désirés pour la DRM CIVA.

## Catalogue des produits spécifiques au portail du CIVA

Le catalogue produit nécessaire aux imports de DRM pour le CIVA est décrit dans le fichier suivant : [Catalogue produit](catalogue_produits.csv) (ce fichier ne traite pas le cas de la colonne mention avec « VT » ou « SGN »)

Ce fichier comporte les différentes colonnes suivantes :

1. La certification : la certification du produit AOC
2. Le genre : Tranquille, Mousseux
3. L'appellation : AOC Alsace blanc, AOC Crémant d'Alsace
4. La mention : vide, VT ou SGN
5. Le lieu : Kanzlerberg, Mambourg... pour les Grands Crus et les communales ; libre pour les lieux dits
6. La couleur : Rouge, Rosé ou Blanc
7. Le cepage : Gewurztraminer, Pinot Blanc, Riesling...

La dernière colonne indique le libellé complet du produit, le processus d'import ne tiendra pas compte de ce champs si les 7 champs d'identification sont remplis. Il sera utilisé que si une ambiguité ressort de l'exploitation de ces champs.

Pour plus de détails sur l'exploitation de ces champs, voir la [section "identification du vin" de la Spécification générique DRM pour logiciels tiers, ](https://github.com/24eme/mutualisation-douane/blob/master/logiciels-tiers/edi/speficication_technique.md#description-des-lignes-cave) .

## Catalogue des mouvements de DRM spécifiques au portail du CIVA

Le catalogue des mouvements de DRM admis par le portail d'du CIVA  [Catalogue mouvements](catalogue_mouvements.csv) est composé de trois colonnes :

1. Le type de DRM : suspendu ou acquitte
2. La catégorie du mouvement : stocks_debut, stocks_fin, entrees ou sorties
3. Le type du mouvement : achatcrd, vrac, repli...

## Exemple complet de fichier d'import de DRM

Un exemple spécifique de DRM à importer pour le portail du CIVA est disponible ici : [Exemple de fichier d'import pour le CIVA](exemple_export_drm.csv) .

Ce fichier reprend l'ensemble des spécificités décrites ci-dessus.
