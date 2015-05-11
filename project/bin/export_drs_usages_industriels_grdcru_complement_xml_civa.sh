#!/bin/bash

. bin/config.inc

ANNEE=$1

if ! test "$ANNEE"; then
	echo "L'année de déclaration de récolte est requise"
	exit;
fi

curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/STATS/_view/DR?reduce=false" | grep "\[\"$ANNEE\",true,true," | grep -Eo "DR-[0-9]+-[0-9]+" > /tmp/drs_validees

echo "campagne;cvi;hash produit;volume total;volume revendiqué;usages industriels;dplc;lies;lies de mouts (ne pas tenir compte)"

while read ligne  
do
	#echo $ligne
    php symfony export:dr-usages-industriels $ligne | grep "GRDCRU" | grep -v ";1$"
done < /tmp/drs_validees