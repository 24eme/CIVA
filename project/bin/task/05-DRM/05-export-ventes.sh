#!/bin/bash

. bin/task.inc

DESCRIPTION="Export CSV des ventes depuis les mouvements de DRM"

. bin/task_start.inc

mkdir -m 777 $TASK_DIR/tmp > /dev/null

EXPORT_FILE=tmp/export_ventes_$(date +%Y%m%d%H%M%S).csv

echo "campagne;periode;identifiant;volume de vente (hl)" > $TASK_DIR/$EXPORT_FILE
cat $TASKDIR/export/bi/export_bi_mouvements.csv | grep ";AOC_ALSACE;" | grep ";SUSPENDU;" | grep -E "(retourmarchandisenontaxees|retourmarchandisetaxees|ventefrancecrd|export|exoversutilisateurauto)" | cut -d ";" -f 3,4,5,21 | awk -F ';' '{ lignes[$2 ";" $3 ";" $1] += $4} END{ for (ligne in lignes) { print ligne ";" lignes[ligne] } }' | sort -t ";" -k 1,3 >> $TASK_DIR/$EXPORT_FILE

echo "[Télécharger le fichier]($TASK_URL/$EXPORT_FILE?$(date +%Y%m%d%H%M%S))"
