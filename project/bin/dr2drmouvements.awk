{
    docid="DR-"$3"-"campagne
    famille=$23
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
        print base_ligne ";superficie;" $13 ";;;;" docid ";" famille;
    }

    suffixe = ""
    if($7 ~ "Rebeche") {
        suffixe = "_rebeche"
    }

    if($14) {
        print base_ligne ";volume" suffixe ";" $14 ";;;;" docid ";" famille;
    }
    if($11 && $1 == $3 && $5 !~ "Jus de raisin") {
        print base_ligne ";volume" suffixe "_cave_particuliere;" $11 ";;;;" docid ";" famille;
    }
    if($11 && $1 != $3) {
        gsub("\"", "", $22);
        gsub("detail_vente_", "", $22);
        print base_ligne ";volume" suffixe "_" $22 ";" $11 ";" $1 ";" $1 ";" $2";" docid ";" famille;
    }
    if($15) {
        print base_ligne ";lies" suffixe ";" $15 ";;;;" docid ";" famille;
    }
    if($17) {
        print base_ligne ";vci" suffixe ";" $17 ";;;;" docid ";" famille;
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
        print base_ligne ";volume_revendique;" $14 - $15 - $17 ";;;;" docid ";" famille;
        print base_ligne ";usages_industriels;" $15 ";;;;" docid ";" famille;
    }

    if(($11 || $10) && $1 == $3) {
        if($10) {
            print base_ligne ";superficie_cave_particuliere;" $10 ";;;;" docid ";" famille;
        }

        if($11) {
            print base_ligne ";volume_revendique_cave_particuliere;" $11 - $12 - $16 ";;;;" docid ";" famille;
        }

        if($12) {
            print base_ligne ";dplc_cave_particuliere;" $12 ";;;;" docid ";" famille;
        }
        if($16) {
            print base_ligne ";vci_cave_particuliere;" $16 ";;;;" docid ";" famille;
        }
    }

    if(($11 || $10) && $1 != $3 && $1 !~ "MOTIF") {
        gsub("\"", "", $22);
        gsub("total_vente_", "", $22);
        if($10) {
            print base_ligne ";superficie_" $22 ";" $10 ";" $1 ";" $1 ";" $2 ";" docid ";" famille;
        }
        if($11) {
            print base_ligne ";volume_revendique_" $22 ";" $11 - $12 - $16 ";" $1 ";" $1 ";" $2 ";" docid ";" famille;
        }
        if($12) {
            print base_ligne ";dplc_" $22 ";" $12 ";" $1 ";" $1 ";" $2 ";" docid ";" famille;
        }
        if($16) {
            print base_ligne ";vci_" $22 ";" $16 ";" $1 ";" $1 ";" $2 ";" docid ";" famille;
        }
    }
}
}
