#!/bin/bash

. bin/config.inc

PERIODE=$1
KEYUNICITE=$(date +%Y%m%d%H%M%S)
TMPDIR=/tmp

cat data/import/Tiers/Tiers-last | awk -vFPAT='([^,]*)|("[^"]+")' -vOFS=, '{ print $1 ";" $4 ";" $58; }' | sed 's/"//g' | grep -v ";$" > $TMPDIR/tiers_db2_$KEYUNICITE.csv

cat data/import/Tiers/Tiers-last | awk -vFPAT='([^,]*)|("[^"]+")' -vOFS=, '{ print $1 ";" $4 ";C" $2; }' | sed 's/"//g' | grep -v ";C0$" >> $TMPDIR/tiers_db2_$KEYUNICITE.csv

cat $TMPDIR/tiers_db2_$KEYUNICITE.csv | sort -t ";" -k 3,3 > $TMPDIR/tiers_db2_$KEYUNICITE.sorted.csv

bash bin/export_ds_csv.sh "propriete" $PERIODE > $TMPDIR/ds_bi_$KEYUNICITE.csv

bash bin/export_ds_csv.sh "negoce" $PERIODE >> $TMPDIR/ds_bi_$KEYUNICITE.csv

cat $TMPDIR/ds_bi_$KEYUNICITE.csv | grep -v "^#" | awk -F ";" '{ appellation=$13 " " $15 " " $14; gsub(/[\ ]+$/, "", appellation); gsub(/[\ ]+/, " ", appellation); type_ds="P"; if($4 ~ /^C/) {type_ds="N"} print $4 ";" type_ds ";" appellation ";" $16 ";" $18 ";" $19 ";" $20 ";" $21}' | sort -t ";" -k 1,1 >> $TMPDIR/ds_bi_$KEYUNICITE.sorted.csv

join -t ";" $TMPDIR/tiers_db2_$KEYUNICITE.sorted.csv $TMPDIR/ds_bi_$KEYUNICITE.sorted.csv -1 3 -2 1 -a 2 | awk -F ";" 'BEGIN {OFS=";"} { identifiant=$1; if ($2 ~ /^[0-9]+$/) { $1=$2; $2=$3; $3=identifiant; } else { $1=""; $2=""; $3=identifiant} print $0; }' | grep -Ev ';(P|N);;' | sort -nt ";" -k 1,1

rm $TMPDIR/tiers_db2_$KEYUNICITE.csv 2> /dev/null
rm $TMPDIR/tiers_db2_$KEYUNICITE.sorted.csv 2> /dev/null
rm $TMPDIR/ds_bi_$KEYUNICITE.csv 2> /dev/null
rm $TMPDIR/ds_bi_$KEYUNICITE.sorted.csv 2> /dev/null
