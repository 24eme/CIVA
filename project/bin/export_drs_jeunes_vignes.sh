#!/bin/bash

. bin/config.inc

ANNEE=$1

if ! test "$ANNEE"; then
	echo "L'année de déclaration de récolte est requise"
	exit
fi

echo "cvi;superficie_jeunes_vignes"

curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/DR/_view/Jeunes-vignes" | grep "\-$ANNEE" | cut -d "," -f 3,4 | sed 's/"//g' | sed 's/\]//' | sed 's/,/;/g' | sed 's/\./,/g'