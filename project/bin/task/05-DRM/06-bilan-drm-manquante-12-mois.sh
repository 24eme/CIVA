#!/bin/bash

. bin/task.inc

DESCRIPTION="Export de deux bilans DRM sur 12 mois pour identifier les DRM manquantes"

. bin/task_start.inc

mkdir -p $TASK_DIR/tmp 2> /dev/null

PREFIX_EXPORT_FILE=tmp/bilan_drm_$(date +%Y%m%d%H%M%S)

curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/drm/_view/derniere?reduce=false" | awk -F '"' '{if ( $10 >= "'$( date  --date="14 months ago" +"%Y%m" )'" && $10 <= "'$( date  --date="2 months ago" +"%Y%m" )'" ) print $4; }' | grep -v '\-M' | sed 's/-/ /g'  | awk '{ tab[ $2 ][ $3 ] = 1; mois[$3] = 1 } END{ printf("CVI;"); for (y in mois ) { printf "%s;", y; } print " "; for (i in tab) { printf("%s;", i)  ; for (y in mois ) { printf ( "%s;", tab[i][y] ); }  print " " ;} }'  | sed 's/;;/;øøøøøø;/g' | sed 's/;;/;øøøøøø;/g' > $TASK_DIR/$PREFIX_EXPORT_FILE"_toutes.csv"

head -n $TASK_DIR/$PREFIX_EXPORT_FILE"_toutes.csv" > $TASK_DIR/$PREFIX_EXPORT_FILE"_manquantes.csv"
cat $TASK_DIR/$PREFIX_EXPORT_FILE"_toutes.csv" | grep -v ';1;1;1;1;1;1;1;1;1;1;1;1;1;' >> $TASK_DIR/$PREFIX_EXPORT_FILE"_manquantes.csv"

echo "[Télécharger le fichier des DRM manquantes]($TASK_URL/$PREFIX_EXPORT_FILE"_manquantes.csv"?$(date +%Y%m%d%H%M%S))"
echo "[Télécharger le fichier de toutes les DRM]($TASK_URL/$PREFIX_EXPORT_FILE"_toutes.csv"?$(date +%Y%m%d%H%M%S))"
