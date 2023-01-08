#!/bin/bash

. bin/task.inc

DESCRIPTION="Export de la liste des SV de la campagne $DR_CAMPAGNE"

. bin/task_start.inc

mkdir -m 777 $TASK_DIR/tmp > /dev/null

EXPORT_FILE=tmp/export_liste_"$DR_CAMPAGNE"_$(date +%Y%m%d%H%M%S).csv

echo "CVI;Campagne;Raison sociale;Statut;Date de validation;Email;Telephone" > "$TASK_DIR/$EXPORT_FILE";

bash bin/export_svs_csv.sh "$DR_CAMPAGNE" | tail -n +2 | awk -F';' -v CAMPAGNE="$DR_CAMPAGNE" '{v="EN COURS"; if($17) {v="VALIDEE"} ; print CAMPAGNE ";" $1 ";" $2 ";" v ";" $17 }' | uniq | sort > "$TASK_DIR/$EXPORT_FILE.sv"

curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/DR/_view/Achats" | cut -d '"' -f 8,16 | sed 's/"/;/g' | grep "^$DR_CAMPAGNE;" | cut -d ";" -f 2 | sort | uniq > "$TASK_DIR/$EXPORT_FILE.drachat"

join -a 1 -a 2 -1 2 -2 1 -t ";" "$TASK_DIR/$EXPORT_FILE.sv" "$TASK_DIR/$EXPORT_FILE.drachat" | grep -E "^6(7|8)" | sed -r "s/^([0-9]+)$/\1;$DR_CAMPAGNE;;MANQUANTE;/" > "$TASK_DIR/$EXPORT_FILE.join"

curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/etablissement/_view/all?reduce=false" | cut -d ',' -f 9,17,18 | sed 's/"//g' |  grep -E "^[0-9]+" | sed 's/,null/,/g' | sed 's/,/;/g' | sort -t ";" -k 1,1 | uniq > "$TASK_DIR/$EXPORT_FILE.etablissement"

join -t ";" -1 1 -2 1 "$TASK_DIR/$EXPORT_FILE.join" "$TASK_DIR/$EXPORT_FILE.etablissement" >> "$TASK_DIR/$EXPORT_FILE"

rm "$TASK_DIR/$EXPORT_FILE."*

echo "[Télécharger le fichier]($TASK_URL/$EXPORT_FILE?$(date +%Y%m%d%H%M%S))"
