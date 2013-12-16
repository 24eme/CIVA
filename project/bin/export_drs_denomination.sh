#!/bin/bash

. bin/config.inc

ANNEE=$1

if ! test "$ANNEE"; then
echo "L'année de déclaration de récolte est requise"
fi