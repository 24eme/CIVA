#!/bin/bash

PATH_DB2_ACTIF=$1
PATH_DB2_CLOTURE=$2

PATH_TIERS=data/import/Tiers/$(date +%Y%m%d)
PATH_TIERS_LAST=data/import/Tiers/Tiers-last
cat $PATH_DB2_ACTIF $PATH_DB2_CLOTURE | iconv -f ISO-8859-1 -t utf-8 > $PATH_TIERS
echo "Copie et conversion en utf-8 de $1 et $2 dans $PATH_TIERS"
cp $PATH_TIERS $PATH_TIERS_LAST
echo "Copie de $PATH_TIERS dans $PATH_TIERS_LAST"
