#!/bin/bash

echo "_id;type;db2/num;db2/no_stock;cvi;civaba;email"

cat $1 | sed 's/* ()//g' | cut -d ";" -f 1,2,3,4,5,6,14 | grep '*'