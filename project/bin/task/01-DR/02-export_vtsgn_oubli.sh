#!/bin/bash

. bin/task.inc

DESCRIPTION="Export CSV des DR $DR_CAMPAGNE ayant des vtsgn déclarés dans les dénominations"

. bin/task_start.inc

mkdir -m 777 $TASK_DIR/tmp > /dev/null

EXPORT_FILE=tmp/export_drs_vtsgn_oubli_"$DR_CAMPAGNE"_$(date +%Y%m%d%H%M%S).csv

bash bin/export_drs_vtsgn_oubli.sh $DR_CAMPAGNE > $TASK_DIR/$EXPORT_FILE

echo "[Télécharger le fichier]($TASK_URL/$EXPORT_FILE?$(date +%Y%m%d%H%M%S))"
