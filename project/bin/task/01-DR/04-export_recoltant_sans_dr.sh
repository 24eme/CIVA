#!/bin/bash

. bin/task.inc

DESCRIPTION="Export des récoltants n'ayant pas de DR $DR_CAMPAGNE par rapport à la liste des tiers db2 non cloturés et top récolte à oui"

. bin/task_start.inc

mkdir -m 777 $TASK_DIR/tmp > /dev/null

EXPORT_FILE=tmp/export_recoltant_sans_dr_"$DR_CAMPAGNE"_$(date +%Y%m%d%H%M%S).csv

bash bin/export_recoltant_sans_dr.sh $DR_CAMPAGNE > $TASK_DIR/$EXPORT_FILE

echo "[Télécharger le fichier]($TASK_URL/$EXPORT_FILE)"
