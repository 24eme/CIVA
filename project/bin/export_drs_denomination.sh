#!/bin/bash

. bin/config.inc

ANNEE=$1

if ! test "$ANNEE"; then
echo "L'année de déclaration de récolte est requise"
exit;
fi

curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/DR/_view/denomination" | grep "\"$ANNEE\"," | cut -d "," -f 3,4,5,6 | sed 's/"//g' | sed 's/\],value:1}//' | sed 's/,/;/g'
