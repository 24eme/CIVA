#!/bin/bash

. bin/config.inc

ANNEE=$1

if ! test "$ANNEE"; then
    echo "L'année de déclaration est requise"
    exit;
fi

php symfony sv:export-csv "$ANNEE"
