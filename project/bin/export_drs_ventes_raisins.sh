#!/bin/bash

echo "cvi_vendeur;appellation;cvi_acheteur;type;volume;superficie;dontdplc"
bash bin/export_drs_ventes.sh 2013 | grep -E ";(negoces|cooperatives);" | sort