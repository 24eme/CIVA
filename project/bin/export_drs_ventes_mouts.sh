#!/bin/bash

ANNEE=$1

if ! test "$ANNEE"; then
	echo "L'année de déclaration de récolte est requise"
	exit
fi

echo "cvi_vendeur;appellation;cvi_acheteur;type;volume;superficie;dontdplc"
bash bin/export_drs_ventes.sh $ANNEE | grep ";mouts;"