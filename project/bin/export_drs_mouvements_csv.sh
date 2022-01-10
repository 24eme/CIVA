#!/bin/bash

. bin/config.inc

ANNEE=$1

if ! test "$ANNEE"; then
    echo "L'année de déclaration de récolte est requise"
    exit;
fi

echo "type;annee;identifiant;cvi;nom;appellation;lieu;cepage;vtsgn;lieudit;denomination;type mouvement;quantite;identifiant acheteur;cvi acheteur;nom acheteur;doc id"

bash bin/export_drs_csv.sh $ANNEE | grep -v "hash_produit" | awk -v campagne="$ANNEE" -F ";" '{
    docid="DR-"$3"-"campagne
    gsub(/"/, "", docid)
    lieu=$6;
    lieudit="";
    if($5 ~ "Lieu-dit") {
        lieu = "";
        lieudit = $6;
    }
if($6 !~ "TOTAL" && $7 !~ "TOTAL") {
    base_ligne="DR;" campagne ";" $3 ";" $3 ";" $4 ";" $5 ";" lieu ";" $7 ";" $8 ";" lieudit ";" $9;
    if($13) {
        print base_ligne ";superficie;" $13 ";;;;"docid;
    }

    suffixe = ""
    if($7 ~ "Rebeche") {
        suffixe = "_rebeche"
    }

    if($14) {
        print base_ligne ";volume" suffixe ";" $14 ";;;;"docid;
    }
    if($11 && $1 == $3 && $5 !~ "Jus de raisin") {
        print base_ligne ";volume" suffixe "_cave_particuliere;" $11 ";;;;"docid;
    }
    if($11 && $1 != $3) {
        gsub("\"", "", $22);
        gsub("detail_vente_", "", $22);
        print base_ligne ";volume" suffixe "_" $22 ";" $11 ";" $1 ";" $1 ";" $2";"docid;
    }
    if($15) {
        print base_ligne ";lies" suffixe ";" $15 ";;;;"docid;
    }
    if($17) {
        print base_ligne ";vci" suffixe ";" $17 ";;;;"docid;
    }
}
if( $7 ~ "TOTAL" ) {
    gsub("\"", "", $7);
    gsub(/TOTAL ?/, "", $7);

    base_ligne="DR;" campagne ";" $3 ";" $3 ";" $4 ";" $5 ";" lieu ";" $7 ";" $8 ";" lieudit ";"

    if(campagne >= 2021 && ($7 == "Blanc" || $7 == "Rouge"))  {

        next;
    }

    if($1 == $3 && $7 && $7 !~ "Rebeche" && $7 != "Blanc" && $7 != "Rouge") {
        print base_ligne ";volume_revendique;" $14 - $15 - $17 ";;;;"docid;
        print base_ligne ";usages_industriels;" $15 ";;;;"docid;
    }

    if(($11 || $10) && $1 == $3) {
        if($10) {
            print base_ligne ";superficie_cave_particuliere;" $10 ";;;;"docid;
        }

        if($11) {
            print base_ligne ";volume_revendique_cave_particuliere;" $11 - $12 - $16 ";;;;"docid;
        }

        if($12) {
            print base_ligne ";dplc_cave_particuliere;" $12 ";;;;"docid;
        }
        if($16) {
            print base_ligne ";vci_cave_particuliere;" $16 ";;;;"docid;
        }
    }

    if(($11 || $10) && $1 != $3 && $1 !~ "MOTIF") {
        gsub("\"", "", $22);
        gsub("total_vente_", "", $22);
        if($10) {
            print base_ligne ";superficie_" $22 ";" $10 ";" $1 ";" $1 ";" $2 ";" docid;
        }
        if($11) {
            print base_ligne ";volume_revendique_" $22 ";" $11 - $12 - $16 ";" $1 ";" $1 ";" $2 ";" docid;
        }
        if($12) {
            print base_ligne ";dplc_" $22 ";" $12 ";" $1 ";" $1 ";" $2 ";" docid;
        }
        if($16) {
            print base_ligne ";vci_" $22 ";" $16 ";" $1 ";" $1 ";" $2 ";" docid;
        }
    }
}
}' | sort | uniq
