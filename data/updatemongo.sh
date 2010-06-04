#!/bin/bash
# Helper script to reload MongoDB database and reindex fields
#

# set some environment shizzle
# TODO: incorporate logic to override from STDIN
DBNAME="totsy"
FILES="*.json"
EXTENSION="json"
INDEXES="indexes.js"

# fetch a list of data files for import, and loop through that list
for i in $FILES
do
	echo "Importing data for collection $i..."
	mongoimport -d $DBNAME -c ${i%%.$EXTENSION} --drop $i
done

# create indexes and constraints
echo "Creating all indexes and constraints..."
mongo totsy $INDEXES

# done
echo "Done."