#!/bin/bash

cat $1 | cut -d ";" -f -14,16- | grep -E "(\*|CIVABA)"
