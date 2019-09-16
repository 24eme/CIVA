#!/bin/bash

. bin/task.inc

TASK_DIR=$(pwd)/$TASK_DIR
EXPORT_DIR=drm/export_drm_db2_mois
LINK="$TASK_URL/$EXPORT_DIR/?C=M;O=D"
DESCRIPTION="Export des DRM pour DB2 (mois précédent) [Voir tous les exports]($LINK)"

. bin/task_start.inc

mkdir -m 777 -p $TASK_DIR/$EXPORT_DIR > /dev/null

cd $GIILDA_DIR

PERIODEMAX=$(date --date='1 month ago 10 day ago' +%Y%m)

mkdir -m 777 -p $TASK_DIR/$EXPORT_DIR > /dev/null

LOGFILE="$TASK_DIR/$EXPORT_DIR/$(date +%Y%m%d%H%M%S).log"
php symfony drm:export-db2 $TASK_DIR/$EXPORT_DIR --periode_max="$PERIODEMAX" --periode_min="$PERIODEMAX" --application=civa > $LOGFILE 2>&1
echo "[Voir les fichiers]($LINK)"
