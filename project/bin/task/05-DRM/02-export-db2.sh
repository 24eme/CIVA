#!/bin/bash

. bin/task.inc

DESCRIPTION="Export des DRM pour DB2"

. bin/task_start.inc

EXPORT_DIR=contrat/export_drm_db2
LINK="$TASK_URL/$EXPORT_DIR/?C=M;O=D"
EXPORT_DIR=$(pwd)/contrat/export_drm_db2

mkdir -m 777 $EXPORT_DIR > /dev/null

cd $GIILDA_DIR

php symfony drm:export-db2 $EXPORT_DIR --application=civa

echo "[Télécharger le fichier]($TASK_URL/$EXPORT_FILE)"
