#!/bin/bash
# Helper script to reload MongoDB database and reindex fields
#

# set some environment shizzle
DBNAME="totsy"
FILES="*.json"
EXTENSION="json"
INDEXES="indexes.js"

# fetch a list of data files for import

for i in $FILES
do
	COLL="${i%%.$EXTENSION}"
	#echo "We got a datafile called $i for collection $COLL"
	mongoimport -d $DBNAME -c $COLL --drop $i
done

# shell example:
#    "mongoimport -d totsy -c lists --drop lists.json"

# now apply the indexes with the following syntax:
#   "mongo totsy indexes.js"

mongo totsy indexes.js


# done
echo "Done."