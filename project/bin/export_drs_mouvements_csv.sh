#!/bin/bash

. bin/config.inc

ANNEE=$1

if ! test "$ANNEE"; then
    echo "L'année de déclaration de récolte est requise"
    exit;
fi

echo "type;annee;cvi;nom;appellation;lieu;cepage;vtsgn;lieudit;denomination;type mouvement;quantite;cvi acheteur;nom acheteur"

bash bin/export_drs_csv.sh $ANNEE | grep -v "hash_produit" | awk -v campagne="$ANNEE" -F ";" '{
    lieu=$6;
    lieudit="";
    if($5 ~ "Lieu-dit") {
        lieu = "";
        lieudit = $6;
    }
if($6 !~ "TOTAL" && $7 !~ "TOTAL") {
    base_ligne="DR;" campagne ";" $3 ";" $4 ";" $5 ";" lieu ";" $7 ";" $8 ";" lieudit ";" $9;
    if($13) {
        print base_ligne ";superficie;" $13 ";;";
    }

    suffixe = ""
    if($7 ~ "Rebeche") {
        suffixe = "_rebeche"
    }

    if($14) {
        print base_ligne ";volume" suffixe ";" $14 ";;";
    }
    if($11 && $1 == $3 && $5 !~ "Jus de raisin") {
        print base_ligne ";volume" suffixe "_cave_particuliere;" $11 ";;";
    }
    if($11 && $1 != $3) {
        gsub("\"", "", $22);
        gsub("detail_vente_", "", $22);
        print base_ligne ";volume" suffixe "_" $22 ";" $11 ";" $1 ";" $2;
    }
    if($15) {
        print base_ligne ";lies" suffixe ";" $15 ";;";
    }
    if($17) {
        print base_ligne ";vci" suffixe ";" $17 ";;";
    }
}
if( $7 ~ "TOTAL" ) {
    gsub("\"", "", $7);
    gsub(/TOTAL ?/, "", $7);

    base_ligne="DR;" campagne ";" $3 ";" $4 ";" $5 ";" lieu ";" $7 ";" $8 ";" lieudit ";"

    if($1 == $3) {
        print base_ligne ";volume_revendique;" $14 - $15 - $17 ";;";
        print base_ligne ";usages_industriels;" $15 ";;";
    }

    if($11 && $1 == $3) {
        if($10) {
            print base_ligne ";superficie_cave_particuliere;" $10 ";;";
        }

        if($11 && length($12) > 0) {
            print base_ligne ";volume_revendique_cave_particuliere;" $11 - $12 - $16 ";;";
        }

        if(length($12) > 0) {
            print base_ligne ";dplc_cave_particuliere;" $12 ";;";
        }
        if($16) {
            print base_ligne ";vci_cave_particuliere;" $16 ";;";
        }
    }

    if($11 && $1 != $3) {
        gsub("\"", "", $22);
        gsub("total_vente_", "", $22);
        if($10) {
            print base_ligne ";superficie_" $22 ";" $10 ";" $1 ";" $2;
        }
        if($11 && length($12) > 0) {
            print base_ligne ";volume_revendique_" $22 ";" $11 - $12 - $16 ";" $1 ";" $2;
        }
        if(length($12) > 0) {
            print base_ligne ";dplc_" $22 ";" $12 ";" $1 ";" $2;
        }
        if($16) {
            print base_ligne ";vci_" $22 ";" $16 ";" $1 ";" $2;
        }
    }
}
}' | sort | uniq
