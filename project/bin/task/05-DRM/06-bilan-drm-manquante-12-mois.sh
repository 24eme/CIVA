#!/bin/bash

. bin/task.inc

DESCRIPTION="Export de deux bilans DRM sur 12 mois pour identifier les DRM manquantes"

. bin/task_start.inc

mkdir -p $TASK_DIR/tmp 2> /dev/null

PREFIX_EXPORT_FILE=tmp/bilan_drm_$(date +%Y%m%d%H%M%S)

cat $TASK_DIR/export/bi/export_bi_etablissements.csv | grep -v ";SUSPENDU;" | awk -F ';' '{print $5";"$6";"$2}' | sed 's/ETABLISSEMENT-//' | sort  -t ';' -k 1,1 > $TASK_DIR/$PREFIX_EXPORT_FILE"_etablissements.csv.tmp"
curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/drm/_view/derniere?reduce=false" | awk -F '"' '{if ( $10 >= "'$( date  --date="13 months ago" +"%Y%m" )'" && $10 <= "'$( date  --date="40 days ago" +"%Y%m" )'" ) print $4; }' | grep -v '\-M' | sed 's/-/ /g'  | awk '{ tab[ $2 ][ $3 ] = 1; mois[$3] = 1 } END{ printf("CVI;"); for (y in mois ) { printf "%s;", y; } print " "; for (i in tab) { printf("%s;", i)  ; for (y in mois ) { printf ( "%s;", tab[i][y] ); }  print " " ;} }'  | sed 's/;;/;øøøøøø;/g' | sed 's/;;/;øøøøøø;/g' > $TASK_DIR/$PREFIX_EXPORT_FILE"_toutes.csv.tmp"
tail -n +2 $TASK_DIR/$PREFIX_EXPORT_FILE"_toutes.csv.tmp" | sort > $TASK_DIR/$PREFIX_EXPORT_FILE"_toutes.csv.sorted.tmp"

head -n 1 $TASK_DIR/$PREFIX_EXPORT_FILE"_toutes.csv.tmp" | sed 's/CVI;/CVI;Raison sociale;Statut;/' > $TASK_DIR/$PREFIX_EXPORT_FILE"_toutes.csv"
join -t ';' -1 1 -2 1 -a 2 $TASK_DIR/$PREFIX_EXPORT_FILE"_etablissements.csv.tmp" $TASK_DIR/$PREFIX_EXPORT_FILE"_toutes.csv.sorted.tmp" >> $TASK_DIR/$PREFIX_EXPORT_FILE"_toutes.csv"

head -n 1 $TASK_DIR/$PREFIX_EXPORT_FILE"_toutes.csv.tmp" | sed 's/CVI;/CVI;Raison sociale;Statut;/' > $TASK_DIR/$PREFIX_EXPORT_FILE"_manquantes.csv"
cat $TASK_DIR/$PREFIX_EXPORT_FILE"_toutes.csv" | grep  ';øø' | grep -v SUSPENDU  | grep -v '^75' >> $TASK_DIR/$PREFIX_EXPORT_FILE"_manquantes.csv"

rm $TASK_DIR/$PREFIX_EXPORT_FILE"_"*".tmp"

echo "[Télécharger le fichier des DRM manquantes]($TASK_URL/$PREFIX_EXPORT_FILE"_manquantes.csv"?$(date +%Y%m%d%H%M%S))"
echo "[Télécharger le fichier de toutes les DRM]($TASK_URL/$PREFIX_EXPORT_FILE"_toutes.csv"?$(date +%Y%m%d%H%M%S))"
