#!/bin/bash

. bin/task.inc

DESCRIPTION="Export de la liste des SV de la campagne $DR_CAMPAGNE"

. bin/task_start.inc

mkdir -m 777 $TASK_DIR/tmp > /dev/null

EXPORT_FILE=tmp/export_liste_"$DR_CAMPAGNE"_$(date +%Y%m%d%H%M%S).csv

echo "Campagne;CVI;Raison sociale;Statut;Date de validation" > "$TASK_DIR/$EXPORT_FILE";

bash bin/export_svs_csv.sh "$DR_CAMPAGNE" | tail -n +2 | awk -F';' -v CAMPAGNE="$DR_CAMPAGNE" '{v=""; if($17) {v="VALIDEE"} ; print CAMPAGNE ";" $1 ";" $2 ";" v ";" $17 }' | uniq | sort >> "$TASK_DIR/$EXPORT_FILE"

echo "[Télécharger le fichier]($TASK_URL/$EXPORT_FILE?$(date +%Y%m%d%H%M%S))"
