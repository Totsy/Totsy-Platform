#!/bin/bash
# Helper script to reload MongoDB database and reindex fields
#
# This script will parse a directory for files with a given
# extension, then import each one and then run a .js file to
# create indexes and constraints.
#
# This is a great way to keep your data and constraints in
# revision control while under active development.
#
#    - DBNAME - the name of the MongoDB database
#    - FILES - used to find all the json files for import
#    - EXTENSION - the filename extension
#    - INDEXES - this is the file that has all indexes
#
# USAGE
# From within your data directory, call this script:
#   ./updatemongo.sh
#
# TODO: incorporate logic to override from STDIN
DBNAME="totsy"
FILES="*.json"
EXTENSION="json"
INDEXES="indexes.js"

# fetch a list of files for import, and loop through that list
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