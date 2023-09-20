
. bin/config.inc

cd $GIILDA_BASEDIR
bash bin/export_bi_to_zip
cd -

cat $PATH_MISEADISPO_CIVA/ds/*csv | head -n 1 > data/ds.utf8.csv
for csv in $PATH_MISEADISPO_CIVA/ds/*csv ; do
    tail -n +2 $csv
done >> data/ds.utf8.csv

iconv -f UTF8 -t ISO88591//TRANSLIT data/ds.utf8.csv > $PATH_MISEADISPO_CIVA/export/bi/export_bi_ds.csv
cp data/ds.utf8.csv $PATH_MISEADISPO_CIVA/export/bi/export_bi_ds.utf8.csv

echo 'type;annee;"CVI acheteur";"nom acheteur";"CVI recoltant";"nom recoltant";"appellation";"lieu";"cepage";"vtsgn";"denomination";"superficie";"volume";"dont volume a detruire";"superficie totale";"volume total";"volume a detruire total";"dont vci";"vci total";"date de validation";"date de modification";"validateur";"hash_produit";"type_ligne";"famille calculee"' > data/dr.utf8.csv
for (( i=2017; i <= $(date +"%Y"); i++ ));
do
    bash bin/export_drs_csv.sh $i | grep -v "hash_produit" | awk -v campagne="$i" -F ";" '{ print "DR;" campagne ";" $0}' >> data/dr.utf8.csv
done

iconv -f UTF8 -t ISO88591//TRANSLIT data/dr.utf8.csv > $PATH_MISEADISPO_CIVA/export/bi/export_bi_dr.csv
cp data/dr.utf8.csv $PATH_MISEADISPO_CIVA/export/bi/export_bi_dr.utf8.csv

CSVHEADER="type;annee;cvi;identifiant;nom;appellation;lieu;cepage;vtsgn;lieudit;denomination;type mouvement;quantite;identifiant acheteur;cvi acheteur;nom acheteur;doc id;famille calculee"
echo $CSVHEADER > data/dr_mouvements.utf8.csv
for (( i=2017; i <= $(date +"%Y"); i++ ));
do
    bash bin/export_drs_mouvements_csv.sh $i | grep -v ";quantite" >> data/dr_mouvements.utf8.csv
done

iconv -f UTF8 -t ISO88591//TRANSLIT data/dr_mouvements.utf8.csv > $PATH_MISEADISPO_CIVA/export/bi/export_bi_dr_mouvements.csv
cp data/dr_mouvements.utf8.csv $PATH_MISEADISPO_CIVA/export/bi/export_bi_dr_mouvements.utf8.csv

cat data/dr_mouvements.utf8.csv | cut -d ";" -f 2 | sort | uniq | grep -E "[0-9]+" | while read annee; do
    mkdir $PATH_MISEADISPO_CIVA/export/bi/$annee 2> /dev/null;
    echo $CSVHEADER > $PATH_MISEADISPO_CIVA/export/bi/$annee/"$annee"_export_bi_dr_mouvements.utf8.csv;
    cat data/dr_mouvements.utf8.csv | grep -E "^DR;$annee;" >> $PATH_MISEADISPO_CIVA/export/bi/$annee/"$annee"_export_bi_dr_mouvements.utf8.csv;
    iconv -f UTF8 -t ISO88591//TRANSLIT $PATH_MISEADISPO_CIVA/export/bi/$annee/"$annee"_export_bi_dr_mouvements.utf8.csv > $PATH_MISEADISPO_CIVA/export/bi/$annee/"$annee"_export_bi_dr_mouvements.csv;
done;

echo 'type;annee;"CVI acheteur";"Nom acheteur";"CVI récoltant";"Nom récoltant";Appellation;Lieu;Cépage;VTSGN;Dénomination;"Superficie livrée";"Qté livrée en kg";"Volume livré";"Volume à détruire";"Dont VCI";"Volume revendiqué";Type;"Date de validation";"Hash produit";"Document ID"' > data/sv.utf8.csv
for (( i=2017; i <= $(date +"%Y"); i++ ));
do
    bash bin/export_svs_csv.sh $i | grep -v "Hash produit" | awk -v campagne="$i" -F ";" '{ print "SV;" campagne ";" $0}' >> data/sv.utf8.csv
done

iconv -f UTF8 -t ISO88591//TRANSLIT data/sv.utf8.csv > $PATH_MISEADISPO_CIVA/export/bi/export_bi_sv.csv
cp data/sv.utf8.csv $PATH_MISEADISPO_CIVA/export/bi/export_bi_sv.utf8.csv

php symfony sv:export-mouvements-csv 0 --onlyheaders=1 > data/sv_mouvements.utf8.csv
for (( i=2017; i <= $(date +"%Y"); i++ ));
do
    php symfony sv:export-mouvements-csv $i --noheaders=1 >> data/sv_mouvements.utf8.csv
done

iconv -f UTF8 -t ISO88591//TRANSLIT data/sv_mouvements.utf8.csv > $PATH_MISEADISPO_CIVA/export/bi/export_bi_sv_mouvements.csv
cp data/sv_mouvements.utf8.csv $PATH_MISEADISPO_CIVA/export/bi/export_bi_sv_mouvements.utf8.csv

iconv -f UTF8 -t ISO88591//TRANSLIT data/mercuriales/datas_mercuriale.csv > $PATH_MISEADISPO_CIVA/export/bi/export_bi_multicontrats.csv
cp data/mercuriales/datas_mercuriale.csv $PATH_MISEADISPO_CIVA/export/bi/export_bi_multicontrats.utf8.csv

cat $PATH_MISEADISPO_CIVA/export/bi/export_bi_factures.utf8.csv | sort | uniq | awk -F ';' 'BEGIN { } { if($2 != "date") { dates[$2]=$2 }; if($15 == "LIGNE") { montants_ht[$2]=montants_ht[$2]+$11 } if($15 == "TVA") { montants_tva[$2]=montants_tva[$2]+$11 } if($15 == "ECHEANCE") { montants_ttc[$2]=montants_ttc[$2]+$11 } volumes[$2]=volumes[$2]+$21; if($25 == "2") { prelevements_montant[$2] = prelevements_montant[$2] + $11; prelevements_nb[$2] = prelevements_nb[$2] + 1;}} END { print "date;montant ht;tva;montant_ttc;volume;nb_prelevements;prelevements_montant"; for( date in dates ) { print sprintf("%s;%0.2f;%0.2f;%0.2f;%0.2f;%d;%0.2f", date, montants_ht[date], montants_tva[date], montants_ttc[date], volumes[date], prelevements_nb[date], prelevements_montant[date]) } }' | sort -rt ";" -k 1,1 > $PATH_MISEADISPO_CIVA/export/bi/export_bi_factures_synthese.utf8.csv
iconv -f UTF8 -t ISO88591//TRANSLIT $PATH_MISEADISPO_CIVA/export/bi/export_bi_factures_synthese.utf8.csv > $PATH_MISEADISPO_CIVA/export/bi/export_bi_factures_synthese.csv

cat $PATH_MISEADISPO_CIVA/export/bi/export_bi_factures.utf8.csv | grep -v "date" | cut -d ";" -f 2 | cut -d "-" -f 1 | sort | uniq | while read annee; do
    grep -E "^([^;]+;$annee|code journal)" $PATH_MISEADISPO_CIVA/export/bi/export_bi_factures.utf8.csv | sort | uniq > $PATH_MISEADISPO_CIVA/export/bi/$annee/"$annee"_export_bi_factures.utf8.csv;
    iconv -f UTF8 -t ISO88591//TRANSLIT $PATH_MISEADISPO_CIVA/export/bi/$annee/"$annee"_export_bi_factures.utf8.csv > $PATH_MISEADISPO_CIVA/export/bi/$annee/"$annee"_export_bi_factures.csv;
    grep -E "^($annee|date)" $PATH_MISEADISPO_CIVA/export/bi/export_bi_factures_synthese.utf8.csv | sort | uniq | sort -r > $PATH_MISEADISPO_CIVA/export/bi/$annee/"$annee"_export_bi_factures_synthese.utf8.csv;
    iconv -f UTF8 -t ISO88591//TRANSLIT $PATH_MISEADISPO_CIVA/export/bi/$annee/"$annee"_export_bi_factures_synthese.utf8.csv > $PATH_MISEADISPO_CIVA/export/bi/$annee/"$annee"_export_bi_factures_synthese.csv;
done;

cd $GIILDA_BASEDIR

php symfony export:comptes-csv $SYMFONYTASKOPTIONS > $BASEDIR/$PATH_MISEADISPO_CIVA/export/bi/export_bi_comptes.utf8.csv
iconv -f UTF8 -t ISO88591//TRANSLIT $BASEDIR/$PATH_MISEADISPO_CIVA/export/bi/export_bi_comptes.utf8.csv > $BASEDIR/$PATH_MISEADISPO_CIVA/export/bi/export_bi_comptes.csv

cat $BASEDIR/$PATH_MISEADISPO_CIVA/export/bi/export_bi_comptes.utf8.csv | grep -E ';("ETABLISSEMENT"|type);' > $BASEDIR/$PATH_MISEADISPO_CIVA/export/bi/export_bi_comptes_etablissements.utf8.csv
iconv -f UTF8 -t ISO88591//TRANSLIT $BASEDIR/$PATH_MISEADISPO_CIVA/export/bi/export_bi_comptes_etablissements.utf8.csv > $BASEDIR/$PATH_MISEADISPO_CIVA/export/bi/export_bi_comptes_etablissements.csv

cd -

if test "$METABASE_SQLITE"; then
    cp $METABASE_SQLITE $METABASE_SQLITE".tmp"
    python $BASEDIR"/bin/csv2sql.py" $METABASE_SQLITE".tmp" $PATH_MISEADISPO_CIVA/export/bi
    mv -f $METABASE_SQLITE".tmp" $METABASE_SQLITE
fi
