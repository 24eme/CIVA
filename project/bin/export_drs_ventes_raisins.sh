#!/bin/bash

bash bin/exports_ventes_mouts.sh 2013 | grep -E ";(negoces|cooperatives);" | sort