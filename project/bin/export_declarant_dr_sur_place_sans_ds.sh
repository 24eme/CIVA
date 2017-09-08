#!/bin/bash

. bin/config.inc

ANNEE_DR=$1
PERIODE_DS=$2

if ! test "$ANNEE_DR"; then
    echo "L'année de la DR est requise"
    exit;
fi

if ! test "$PERIODE_DS"; then
    echo "La période de la DS est requise"
    exit;
fi

bash bin/export_drs_csv.sh $ANNEE_DR | grep "SUR PLACE" | grep -v "Jeunes Vignes" |grep -v "Vins sans IG" | cut -d ";" -f 2,3,4,11 | awk -F ';' '{ if($4 == 0 || !$4) { next;} print $2 ";" $3; }' | sed 's/"//g' | sort | uniq  > /tmp/declarants_dr_volume_sur_place.csv

bash bin/export_ds_csv.sh "propriete" "$PERIODE_DS" | awk -F ';' '{ print $4 }' | sort | uniq > /tmp/declarants_ds_propriete.csv

join -v 1 -t ";" -1 1 -2 1 /tmp/declarants_dr_volume_sur_place.csv /tmp/declarants_ds_propriete.csv

#rm /tmp/declarants_dr_volume_sur_place.csv
#rm /tmp/declarants_ds_propriete.csv
