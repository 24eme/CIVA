#!/bin/bash

. bin/task.inc

DESCRIPTION="Export CSV des CVI de récoltant ayant du volume sur place dans la DR $DR_CAMPAGNE et n'ayant pas réalisé de DS pour la période $DS_PERIODE"

. bin/task_start.inc

mkdir -m 777 $TASK_DIR/tmp 2> /dev/null

EXPORT_FILE=tmp/export_cvi_ds_proprietes_manquantes_$(date +%Y%m%d%H%M%S).csv

bash bin/export_declarant_dr_sur_place_sans_ds.sh "$DR_CAMPAGNE" "$DS_PERIODE" > $TASK_DIR/$EXPORT_FILE

echo "[Télécharger le fichier]($TASK_URL/$EXPORT_FILE?$(date +%Y%m%d%H%M%S))"
