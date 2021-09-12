#!/bin/bash

. bin/task.inc

DRM_PERIODE=$(echo $DS_PERIODE | sed -r 's/07$/08/');

DESCRIPTION="Export CSV des identifiants des opérateurs ayant déclarés leur DRM d'août ($DRM_PERIODE) et n'ayant pas réalisé de DS pour la période $DS_PERIODE"

. bin/task_start.inc

mkdir -m 777 $TASK_DIR/tmp 2> /dev/null

EXPORT_FILE=tmp/export_cvi_ds_manquantes_drm_$(date +%Y%m%d%H%M%S).csv

curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/drm/_view/all?reduce=false" | grep -E "\-($DS_PERIODE|$DRM_PERIODE)" | cut -d "," -f 2,16 | sed 's/"key":\["//' | sed 's/","/;/'  |sed 's/\"]//' | sort | uniq | while read ligne; do echo -n "$ligne;"; curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/DS-$(echo -n $ligne | cut -d ";" -f 1)-$DS_PERIODE-001"; done  | grep "missing" | cut -d ";" -f 1,2 > $TASK_DIR/$EXPORT_FILE

echo "[Télécharger le fichier]($TASK_URL/$EXPORT_FILE?$(date +%Y%m%d%H%M%S))"
