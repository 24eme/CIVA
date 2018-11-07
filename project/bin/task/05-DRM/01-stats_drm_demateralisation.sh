#!/bin/bash

. bin/task.inc

DESCRIPTION="Statistiques de la dématérialisation des DRM"

. bin/task_start.inc

mkdir -m 777 $TASK_DIR/tmp > /dev/null

EXPORT_FILE=tmp/stats_drm_demat.csv
curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/drm/_view/all?reduce=false"   | awk -F ',' '{print $2";"$4";"$7";"$12";"$14}'  | sed 's/"key":.//'  | sed 's/null//g'  | sort -t ';' -k 2,2 | grep '"[0-9][0-9]*"'  | awk 'BEGIN{print "cvi;mois drm;date signature;transfer;conforme"} {print $0}' > $TASK_DIR/$EXPORT_FILE
echo "[Télécharger le fichier]($TASK_URL/$EXPORT_FILE)"
