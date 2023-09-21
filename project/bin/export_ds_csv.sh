#!/bin/bash

. bin/config.inc

PERIODE=$1

if ! test "$PERIODE"; then
    echo "La période de stock est requise"
    exit;
fi
URL="http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/STATS/_view/DS?reduce=false"

curl -s "$URL" | grep ",\"$PERIODE\"," | cut -d "," -f 1 | sed 's/{"id":"//' | sed 's/"//' | grep "DS" > /tmp/dss_ids

php symfony export:ds-csv --onlyheader=1 0

while read ligne
do
    php symfony export:ds-csv $ligne
done < /tmp/dss_ids

rm /tmp/dss_ids
