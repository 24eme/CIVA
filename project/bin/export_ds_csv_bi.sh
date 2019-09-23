#!/bin/bash

. bin/config.inc

PERIODE=$1
KEYUNICITE=$(date +%Y%m%d%H%M%S)
TMPDIR=/tmp

cat data/import/Tiers/Tiers-last | awk -vFPAT='([^,]*)|("[^"]+")' -vOFS=, '{ print $1 ";" $4 ";" $58 ";" $18; }' | sed 's/"//g' | grep -v ";$" > $TMPDIR/tiers_db2_$KEYUNICITE.csv

cat data/import/Tiers/Tiers-last | awk -vFPAT='([^,]*)|("[^"]+")' -vOFS=, '{ print $1 ";" $4 ";C" $2 ";" $18; }' | sed 's/"//g' | grep -v ";C0$" >> $TMPDIR/tiers_db2_$KEYUNICITE.csv

cat $TMPDIR/tiers_db2_$KEYUNICITE.csv | sort -t ";" -k 3,3 > $TMPDIR/tiers_db2_$KEYUNICITE.sorted.csv

bash bin/export_ds_csv.sh "propriete" $PERIODE > $TMPDIR/ds_bi_$KEYUNICITE.csv

bash bin/export_ds_csv.sh "negoce" $PERIODE >> $TMPDIR/ds_bi_$KEYUNICITE.csv

cat $TMPDIR/ds_bi_$KEYUNICITE.csv | grep -v "^#" | awk -F ";" '{ appellation=$13 " " $15;lieu=$14; if(!lieu && $17) {lieu=$17} gsub(/[\ ]+$/, "", appellation); gsub(/[\ ]+/, " ", appellation); type_ds="P"; if($4 ~ /^C/) {type_ds="N"; } print substr($1,0,4) ";" $4 ";" type_ds ";" appellation ";" lieu ";" $16 ";" $18 ";" $19 ";" $20 ";" $21}' | grep -Ev ";(P|N);(mouts|rebeches|lies|dplc|);" | sed 's/\"//g' | sort -t ";" -k 1,1 >> $TMPDIR/ds_bi_$KEYUNICITE.sorted.csv

echo "campagne;num tiers;num stock;cvi;civaba;type tiers;type de ds;appellation;lieu;cepage;volume total;volume normal;volume vt;volume sgn"
join -t ";" $TMPDIR/tiers_db2_$KEYUNICITE.sorted.csv $TMPDIR/ds_bi_$KEYUNICITE.sorted.csv -1 3 -2 2 -a 2 | awk -F ";" '{ cvi=""; civaba=""; if($1 ~ /^C/) { civaba=substr($1,2) } else { cvi=$1 } print $5 ";" $2 ";" $3 ";" cvi ";" civaba ";" $4 ";" $6 ";" $7 ";" $8 ";" $9 ";" $10 ";" $11 ";" $12 ";" $13 }' | sort -nt ";" -k 1,2

rm $TMPDIR/tiers_db2_$KEYUNICITE.csv 2> /dev/null
rm $TMPDIR/tiers_db2_$KEYUNICITE.sorted.csv 2> /dev/null
rm $TMPDIR/ds_bi_$KEYUNICITE.csv 2> /dev/null
rm $TMPDIR/ds_bi_$KEYUNICITE.sorted.csv 2> /dev/null
