#!/bin/bash

ls web/mise_a_disposition/ > /tmp/liste_miseadispo

cat /tmp/liste_miseadispo | while read line; do
	mv web/mise_a_disposition/$line/declarations_de_recolte web/mise_a_disposition/$line/DR 2> /dev/null
	mv web/mise_a_disposition/$line/declarations_de_stocks web/mise_a_disposition/$line/DS 2> /dev/null
done;