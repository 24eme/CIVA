#!/bin/bash

. bin/config.inc

ANNEE=$1
DATE=$2

if ! test "$ANNEE"; then
	echo "L'année de déclaration de récolte est requise"
	exit;
fi

if ! test "$DATE"; then
	echo "La date à ne pas dépassé est requise"
	exit;
fi

curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/STATS/_view/DR?reduce=false" | grep "\[\"$ANNEE\",true,true," | grep -Eo "DR-[0-9]+-[0-9]+" > /tmp/drs_validees

while read ligne  
do
	#echo $ligne
    php symfony dr:changeDate $ligne $DATE  
done < /tmp/drs_validees