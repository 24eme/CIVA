
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

echo "Type;Annee;CVI;Nom;Appellation;Lieu;Cepage;VTSGN;Denomination;Superficie;Volume;Lies;Volume Revendique;Usages industriels;VCI" > data/dr.utf8.csv
for (( i=2017; i <= 2020; i++ ));
do
    bash bin/export_drs_csv.sh 2020 | grep -v "hash_produit" | awk -v campagne="$i" -F ";" '{ if($6 !~ "TOTAL" && $7 !~ "TOTAL") { print "DR;" campagne ";" $3 ";" $4 ";" $5 ";" $6 ";" $7 ";" $8 ";" $9 ";" $13 ";" $14 ";" $15 ";;;" $17 } if( $6 ~ "TOTAL" ) { print "DR;2020;" $3 ";" $4 ";" $5 ";;;;;;;;;" $13 - $15 - $17 ";" $15 ";" }}' | sort | uniq >> data/dr.utf8.csv
done

iconv -f UTF8 -t ISO88591//TRANSLIT data/dr.utf8.csv > $PATH_MISEADISPO_CIVA/export/bi/export_bi_dr.csv
cp data/dr.utf8.csv $PATH_MISEADISPO_CIVA/export/bi/export_bi_dr.utf8.csv

iconv -f UTF8 -t ISO88591//TRANSLIT data/mercuriales/datas_mercuriale.csv > $PATH_MISEADISPO_CIVA/export/bi/export_bi_multicontrats.csv
cp data/mercuriales/datas_mercuriale.csv $PATH_MISEADISPO_CIVA/export/bi/export_bi_multicontrats.utf8.csv

if test -f $METABASE_SQLITE ; then
    if test "$METABASE_SQLITE"; then
        cp $METABASE_SQLITE $METABASE_SQLITE".tmp"
        python $BASEDIR"/bin/csv2sql.py" $METABASE_SQLITE".tmp" $PATH_MISEADISPO_CIVA/export/bi
        mv -f $METABASE_SQLITE".tmp" $METABASE_SQLITE
    fi
fi
