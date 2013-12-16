#!/bin/bash

. bin/config.inc

ANNEE=$1

if ! test "$ANNEE"; then
	echo "L'année de déclaration de récolte est requise"
fi

echo "cvi_vendeur;appellation;cvi_acheteur;type;volume;superficie;dontdplc"

curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/DR/_view/Achats" | grep "\-$ANNEE\",\"key" | sed 's/"//g' | sed 's/{id://' | sed "s/,key:\["$ANNEE",/,/" | sed "s/DR-[0-9]*-$ANNEE,//" | sed 's/\[//' | sed 's/\]//' | sed 's/\]\},//' | sed 's/value://' | sed 's/,/;/g' | sed 's/]}//' | grep -E "^[0-9]+" | sed 's/\./,/g'