#!/bin/bash

. bin/config.inc

PERIODE=$1

cat data/import/Tiers/Tiers-last | awk -vFPAT='([^,]*)|("[^"]+")' -vOFS=, '{ print $1 ";" $4 ";" $58; }' | sed 's/"//g' | grep -v ";$" > /tmp/tiers_db2.csv

cat data/import/Tiers/Tiers-last | awk -vFPAT='([^,]*)|("[^"]+")' -vOFS=, '{ print $1 ";" $4 ";C" $2; }' | sed 's/"//g' | grep -v ";$" >> /tmp/tiers_db2.csv

cat /tmp/tiers_db2.csv | sort -t ";" -k 3,3 > /tmp/tiers_db2.sorted.csv

bash bin/export_ds_csv.sh "propriete" $PERIODE > /tmp/ds.csv

bash bin/export_ds_csv.sh "negoce" $PERIODE >> /tmp/ds.csv

cat /tmp/ds.csv | grep -v "^#" | awk -F ";" '{ appellation=$13 " " $15 " " $14; gsub(/[\ ]+$/, "", appellation); gsub(/[\ ]+/, " ", appellation); type_ds="P"; if($4 ~ /^C/) {type_ds="N"} print $4 ";" type_ds ";" appellation ";" $16 ";" $18 ";" $19 ";" $20 ";" $21}' | sort -t ";" -k 1,1 >> /tmp/ds_bi.sorted.csv

join -t ";" /tmp/tiers_db2.sorted.csv /tmp/ds_bi.sorted.csv -1 3 -2 1 -a 2 | awk -F ";" 'BEGIN {OFS=";"} { identifiant=$1; if ($2 ~ /^[0-9]+$/) { $1=$2; $2=$3; $3=identifiant; } else { $1=""; $2=""; $3=identifiant} print $0; }' | sort -t ";" -k 1,1
