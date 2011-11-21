#!/bin/bash
#
# A minimalistic, pragmatic and general purpose build script which helps to
# execute various commands to setup, maintain and test the codebase. Calling the
# script without any arguments will print a list of all available commands.
#
# Dependencies can be expressed by calling this script from itself. A command
# requiring a specific library to be setup may call `$0 source-framework`
# within its section.
#
# To enable debugging (will print out any command as it is
# executed) uncomment the line with `set -x` below.
#

# Error handling
set -o nounset
# set -o errexit

# Debugging
# set -x

# Functions
function print_usage {
	echo ""
	echo "$0 <COMMAND>"
	echo ""
	echo "Available commands are:"
	echo " - init              Initially prepare codebase."
	echo " - fix-perms         Set default permissions on app, admin and resources (excl. libraries)."
	echo " - run-tests         Runs lithium, app, admin and library tests."
	echo " - optimize-repo     Perform GC on local git repository."
	echo " - source-lithium    Install lithium."
	echo " - source-subs       Initialize and update all submodules."
	echp " - clear-cache       Clears file caches on admin and app."
}

if [ $# != 1 ]; then
	print_usage
	exit 1
fi

# Configuration
PROJECT_DIR=$(pwd)

# Arguments
COMMAND=$1

case $COMMAND in
	init)
		echo "Initializing codebase..."

		$0 source-subs
		$0 fix-perms

		FILES=$(find $PROJECT_DIR/{app,admin} -type f -print0 | xargs -0 grep -l -i -E 'ini_set.*display_error.*(off|false|0)')

		echo
		echo
		echo "NOTE: There is currently *no lithium core for the app* shipped with"
		echo "      the codebase. Please ensure to place one at:"
		echo
		echo "      $PROJECT_DIR/libraries/lithium"
		echo
		echo "QA: Some errors are being surpressed in:"
		echo
		for FILE in $FILES; do
			echo "      $FILE"
		done
		echo

		read -p "Do you want to add a lithium core now? (y/n) " CONFIRM
		if [[ $CONFIRM == "y" ]]; then
			$0 source-lithium
		fi

		echo
		echo "Done :-)"
		echo
		;;

	fix-perms)
		echo "Setting permissions on application resource directories/files..."
		find $PROJECT_DIR/{app,admin}/resources -type f -exec chmod -f 0666 {} \;
		find $PROJECT_DIR/{app,admin}/resources -type d -exec chmod -f 0777 {} \;

		echo "Setting permissions on data directory/files..."
		find $PROJECT_DIR/data -type f -exec chmod -f 0640 {} \;
		find $PROJECT_DIR/data -type d -exec chmod -f 0750 {} \;
		;;

	# This section collects all commands required to run
	# all tests contained within libraries and apps across
	# the entire codebase.
	run-tests)
		echo "Running payment related tests..."

		cd $PROJECT_DIR/admin
		libraries/lithium/console/li3 --env=test test tests/cases/controllers/OrdersControllerTest.php
		libraries/lithium/console/li3 --env=test test tests/cases/models/OrderTest.php

		cd $PROJECT_DIR
		libraries/lithium/console/li3 test --case=app.tests.cases.controllers.OrdersController
		libraries/lithium/console/li3 test --case=app.tests.cases.models.OrderTest

		libraries/lithium/console/li3 test --case=li3_payments.tests.integration.TransactionsTest

		echo
		;;

	optimize-repo)
		echo "Optimizing local GIT repository..."
		cd $PROJECT_DIR

		BEFORE=$(du -hs .git)

		git prune
		git gc --aggressive
		git prune-packed
		git repack -a

		AFTER=$(du -hs .git)
		echo "Result: $BEFORE -> $AFTER"
		echo
		;;

	source-subs)
		echo "Updating and initialising all registered submodules recursively..."
		git submodule update --init --recursive
		;;

	source-lithium)
		TARGET=$PROJECT_DIR/libraries/lithium
		TMP=$(mktemp -d /tmp/totsyXXXX)

		echo "Removing old..."
		test -d $TARGET && rm -r $TARGET

		git clone git://github.com/UnionOfRAD/lithium.git $TMP
		cd $TMP
		git checkout -q b4d64753832ec0fa344cd5092571d691ede03176
		mv $TMP/libraries/lithium $TARGET

		echo "Removing history..."
		rm -fr .git

		echo "Removing temporary directory..."
		rm -fr $TMP
		;;

	clear-cache)
		find $PROJECT_DIR/app/resources/tmp/cache -name 'empty' -prune -o -type f | xargs rm -v
		find $PROJECT_DIR/admin/resources/tmp/cache -name 'empty' -prune -o -type f | xargs rm -v
		;;

	*)
		echo "Unknown command '${COMMAND}'."
		exit 1
		;;
esac

exit 0
