#!/bin/bash

. bin/task.inc

DRM_PERIODE=$(echo $DS_PERIODE | sed -r 's/07$/08/');

DESCRIPTION="Export CSV des identifiants des opérateurs ayant déclarés leur DRM d'août ($DRM_PERIODE) et n'ayant pas réalisé de DS pour la période $DS_PERIODE"

. bin/task_start.inc

mkdir -m 777 $TASK_DIR/tmp 2> /dev/null

EXPORT_FILE=tmp/export_cvi_ds_manquantes_drm.csv

curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/drm/_view/all?reduce=false" | grep -E "\-($DS_PERIODE|$DRM_PERIODE)" | cut -d "," -f 1 | cut -d "-" -f 2 | sort | uniq | while read id; do echo -n "$id;"; curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/DS-$id-$DS_PERIODE-001"; done | grep "missing" | cut -d ";" -f 1 > $TASK_DIR/$EXPORT_FILE

echo "[Télécharger le fichier]($TASK_URL/$EXPORT_FILE?$(date +%Y%m%d%H%M%S))"
