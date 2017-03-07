#!/bin/bash

cat $1 | cut -d ";" -f -7,15 | grep -E "(\*|CIVABA)"
