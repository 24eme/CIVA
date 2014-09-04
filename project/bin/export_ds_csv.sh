#!/bin/bash

. bin/config.inc

TYPE=$1
PERIODE=$2

if ! test "$PERIODE"; then
    echo "La période de stock est requise"
    exit;
fi
URL="http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/STATS/_view/DS?reduce=false"

curl -s "$URL" | grep "\"key\":\[\"$TYPE\",\"$PERIODE\"" | cut -d "," -f 1 | sed 's/{"id":"//' | sed 's/"//' | grep "DS" > /tmp/dss_ids

echo "#periode;id_doc;nom;identifiant;numero_stokage;principale;statut;date_validation_tiers;date_validation_civa;date_depot_mairie;appellation;lieu;couleur;cepage;denomination;volume_total;volume_normal;volume_vt;volume_sgn";

while read ligne  
do
    php symfony export:ds-csv $ligne
done < /tmp/dss_ids

rm /tmp/dss_ids
