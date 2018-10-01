#!/bin/bash

. bin/task.inc

EXPORT_DIR=ds
EXPORT_FILE=export_bi_$DS_PERIODE.csv
LINK="$TASK_URL/$EXPORT_DIR/?C=M;O=D"

DESCRIPTION="Export BI pour les DS propriétés et négoces de la période $DS_PERIODE\n
            [Voir tous les fichiers générés]($LINK)"

. bin/task_start.inc

mkdir -m 777 -p $TASK_DIR/$EXPORT_DIR > /dev/null

echo "$DS_PERIODE"
echo "$TASK_DIR/$EXPORT_DIR/$EXPORT_FILE"

bash bin/export_ds_csv_bi.sh "$DS_PERIODE" > $TASK_DIR/$EXPORT_DIR/$EXPORT_FILE.tmp

mv $TASK_DIR/$EXPORT_DIR/$EXPORT_FILE.tmp $TASK_DIR/$EXPORT_DIR/$EXPORT_FILE

echo "[Voir tous les fichiers générés]($LINK)"
echo "[Télécharger le fichier]($TASK_URL/$EXPORT_DIR/$EXPORT_FILE)"
