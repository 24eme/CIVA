
. bin/config.inc

cd $GIILDA_BASEDIR
bash bin/export_bi_to_zip
cd -

rm $PATH_MISEADISPO_CIVA/export/bi/export_bi_contrats.csv
rm $PATH_MISEADISPO_CIVA/export/bi/export_bi_contrats.utf8.csv

cat $PATH_MISEADISPO_CIVA/ds/*csv | head -n 1 > data/ds.utf8.csv
for csv in $PATH_MISEADISPO_CIVA/ds/*csv ; do
    tail -n +2 $csv
done >> data/ds.utf8.csv

iconv -f UTF8 -t ISO88591//TRANSLIT data/ds.utf8.csv > $PATH_MISEADISPO_CIVA/export/bi/export_bi_ds.csv
cp data/ds.utf8.csv $PATH_MISEADISPO_CIVA/export/bi/export_bi_ds.utf8.csv

echo 'type;annee;"CVI acheteur";"nom acheteur";"CVI recoltant";"nom recoltant";"appellation";"lieu";"cepage";"vtsgn";"denomination";"superficie";"volume";"dont volume a detruire";"superficie totale";"volume total";"volume a detruire total";"dont vci";"vci total";"date de validation";"date de modification";"validateur";"hash_produit";"type_ligne"' > data/dr.utf8.csv
for (( i=2017; i <= 2020; i++ ));
do
    bash bin/export_drs_csv.sh $i | grep -v "hash_produit" | awk -v campagne="$i" -F ";" '{ print "DR;" campagne ";" $0}' >> data/dr.utf8.csv
done

iconv -f UTF8 -t ISO88591//TRANSLIT data/dr.utf8.csv > $PATH_MISEADISPO_CIVA/export/bi/export_bi_dr.csv
cp data/dr.utf8.csv $PATH_MISEADISPO_CIVA/export/bi/export_bi_dr.utf8.csv

echo "type;annee;cvi;nom;appellation;lieu;cepage;vtsgn;lieudit;denomination;type mouvement;quantite;cvi acheteur;nom acheteur" > data/dr_mouvements.utf8.csv
for (( i=2017; i <= 2020; i++ ));
do
    bash bin/export_drs_mouvements_csv.sh $i | grep -v ";quantite" >> data/dr_mouvements.utf8.csv
done

iconv -f UTF8 -t ISO88591//TRANSLIT data/dr_mouvements.utf8.csv > $PATH_MISEADISPO_CIVA/export/bi/export_bi_dr_mouvements.csv
cp data/dr_mouvements.utf8.csv $PATH_MISEADISPO_CIVA/export/bi/export_bi_dr_mouvements.utf8.csv

iconv -f UTF8 -t ISO88591//TRANSLIT data/mercuriales/datas_mercuriale.csv > $PATH_MISEADISPO_CIVA/export/bi/export_bi_multicontrats.csv
cp data/mercuriales/datas_mercuriale.csv $PATH_MISEADISPO_CIVA/export/bi/export_bi_multicontrats.utf8.csv

if test "$METABASE_SQLITE"; then
    cp $METABASE_SQLITE $METABASE_SQLITE".tmp"
    python $BASEDIR"/bin/csv2sql.py" $METABASE_SQLITE".tmp" $PATH_MISEADISPO_CIVA/export/bi
    mv -f $METABASE_SQLITE".tmp" $METABASE_SQLITE
fi
