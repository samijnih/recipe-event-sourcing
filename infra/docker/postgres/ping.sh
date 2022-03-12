#!/bin/bash

psql -q -d $POSTGRES_DB -U $POSTGRES_USER -Atc "SELECT 1;" 2> /dev/null
