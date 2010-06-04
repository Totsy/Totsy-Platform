#!/bin/bash
# Helper script to reload MongoDB database and reindex fields
#

# set some environment shizzle
# TODO: incorporate logic to override from STDIN
DBNAME="totsy"
FILES="*.json"
EXTENSION="json"
INDEXES="indexes.js"

# fetch a list of data files for import

for i in $FILES
do
	echo "Importing data for collection $i..."
	mongoimport -d $DBNAME -c ${i%%.$EXTENSION} --drop $i
done

# shell example:
#    "mongoimport -d totsy -c lists --drop lists.json"

# now apply the indexes with the following syntax:
#   "mongo totsy indexes.js"

echo "Creating all indexes and constraints..."
mongo totsy $INDEXES

# done
echo "Done."