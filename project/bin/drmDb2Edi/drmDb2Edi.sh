#!/bin/bash

. bin/config.inc

PATH_ENTREES=data/import/15/drm/DRMDEM1415
PATH_SORTIES_SUSPENDU=data/import/15/drm/DRMDSS1415
PATH_SORTIES_EXONERES=data/import/15/drm/DRMDSE1415
PATH_SORTIES_AUTRES=data/import/15/drm/DRMDSO1415
PATH_SORTIES_ACQUITTE=data/import/15/drm/DRMDSA1415
PATH_EXPORT=data/import/15/drm/DRMAX21415
PATH_CRD=data/import/15/drm/DRMCRD1415

AWK_FUNCTIONS='
function getPrefixe(ligneType) {
    periode=sprintf("%s%02d", $1, $2);
    identifiant=$3;
    accises="";

    return ligneType ";" periode ";" identifiant ";" accises;
}
function printLinesDRM(premiereColonneVolume, typeDRM, catMouvement, typeMouvement, descriptionMouvement, nbColonneVolume) {


    libelle_blanc="AOC Alsace Blanc";
    libelle_rose="AOC Alsace Rosé";
    libelle_grdcru="AOC Alsace Grands Crus";
    libelle_cremant="AOC Alsace Crémant";

    prefixe=getPrefixe("CAVE");

    volume_blanc=$(premiereColonneVolume);
    volume_rose=$(premiereColonneVolume + 1);
    volume_grdcru=$(premiereColonneVolume + 2);
    volume_cremant=$(premiereColonneVolume + 3);

    if(!nbColonneVolume) {
        nbColonneVolume = 4;
    }

    if(nbColonneVolume >= 1 && volume_blanc > 0) {
        print prefixe ";" libelle_blanc ";;;;;;;" typeDRM ";" catMouvement ";" typeMouvement ";" volume_blanc ";;;;;" descriptionMouvement;
    }
    if(nbColonneVolume >= 2 && volume_rose > 0) {
        print prefixe ";" libelle_rose ";;;;;;;" typeDRM ";" catMouvement ";" typeMouvement ";" volume_rose ";;;;;" descriptionMouvement;
    }
    if(nbColonneVolume >= 3 && volume_grdcru > 0) {
        print prefixe ";" libelle_grdcru ";;;;;;;" typeDRM ";" catMouvement ";" typeMouvement ";" volume_grdcru ";;;;;" descriptionMouvement;
    }
    if(nbColonneVolume >= 4 && volume_cremant > 0) {
        print prefixe ";" libelle_cremant ";;;;;;;" typeDRM ";" catMouvement ";" typeMouvement ";" volume_cremant ";;;;;" descriptionMouvement;
    }
}
';

cat $PATH_ENTREES | awk -F ',' '
'"$AWK_FUNCTIONS"'
{
    printLinesDRM(6, "suspendu", "entrees", "revendication", "Entrées");
    printLinesDRM(10, "suspendu", "entrees", "achatnoncrd", "Achats vrac + bouteilles sans CRD (Propriété)");
    printLinesDRM(14, "suspendu", "entrees", "achatnoncrd", "Achats vrac + bouteilles sans CRD (Négociant)");
    printLinesDRM(18, "suspendu", "entrees", "retourmarchandisetaxees", "Quantités réintégrées CVO + Droits circulation 12a");
    printLinesDRM(22, "suspendu", "entrees", "retourmarchandisenontaxees", "Quantités réintégrées CVO seule 12b");
    printLinesDRM(26, "suspendu", "entrees", "repli", "Replis", 1);
}'

cat $PATH_SORTIES_SUSPENDU | awk -F ',' '
'"$AWK_FUNCTIONS"'
{
    printLinesDRM(6, "suspendu", "sorties", "vracsanscontratsuspendu", "B - Hors région Alsace (UE - pays tiers ou autre EA en France)");
    printLinesDRM(10, "suspendu", "sorties", "vrac", "C - Vrac");
    printLinesDRM(14, "suspendu", "sorties", "bouteillenue", "D - Expeditions en Alsace en bouteilles");
}'

cat $PATH_SORTIES_EXONERES | awk -F ',' '
'"$AWK_FUNCTIONS"'
{
    printLinesDRM(6, "suspendu", "sorties", "distillationusageindustriel", "I - Vers un utilisateur autorisé");
    printLinesDRM(10, "suspendu", "sorties", "consommationfamilialedegustation", "J - Dégustations à la propriété");
}'


cat $PATH_SORTIES_AUTRES | awk -F ',' '
'"$AWK_FUNCTIONS"'
{
    printLinesDRM(6, "suspendu", "sorties", "repli", "K - Replis");
    printLinesDRM(10, "suspendu", "sorties", "destructionperte", "L - Lies");
}'

cat $PATH_SORTIES_ACQUITTE | awk -F ',' '
'"$AWK_FUNCTIONS"'
{
    printLinesDRM(6, "acquitte", "sorties", "ventefrancecrd", "A - (75 cl) CRD ou DS/DSAC France");
    printLinesDRM(10, "acquitte", "sorties", "ventefrancecrd", "A - CRD ou DS/DSAC France");
    printLinesDRM(14, "acquitte", "sorties", "vracsanscontratacquitte", "A bis - DSA/DSAC Hors France Métropolitaine");
}'

cat $PATH_EXPORT | awk -F ',' '
'"$AWK_FUNCTIONS"'
{
    print getPrefixe("CAVE") ";AOC Alsace;;;;;;;acquitte;sorties;export;" $8 ";" $7 ";;;;Export 75cl";
    print getPrefixe("CAVE") ";AOC Alsace;;;;;;;acquitte;sorties;export;" $9 ";" $7 ";;;;Export";
    print getPrefixe("CAVE") ";AOC Alsace Crémant;;;;;;;acquitte;sorties;export;" $10 ";" $7 ";;;;Export";
    print getPrefixe("CAVE") ";AOC Alsace Grands Crus;;;;;;;acquitte;sorties;export;" $11 ";" $7 ";;;;Export";
}'

cat $PATH_CRD | awk -F ',' '
'"$AWK_FUNCTIONS"'
{
    print getPrefixe("CRD") ";TRANQUILLE;VERT;Bouteille " $7 " cl;;;;;sorties;utilisations;" $8 ;
    print getPrefixe("CRD") ";MOUSSEUX;BLEU;Bouteille " $7 " cl;;;;;sorties;utilisations;" $9 ;
    print getPrefixe("CRD") ";TRANQUILLE;VERT;Bouteille " $7 " cl;;;;;sorties;destructions;" $10 ;
    print getPrefixe("CRD") ";MOUSSEUX;BLEU;Bouteille " $7 " cl;;;;;sorties;destructions;" $11 ;
}'
