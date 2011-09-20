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

		FILES=$(find $PROJECT_DIR/admin -type f -print0 | xargs -0 grep -l -i -E 'ini_set.*display_error.*(off|false|0)')

		echo
		echo "NOTE: There is currently *no lithium core for the app* shipped with"
		echo "      the codebase. Please ensure to place one manually in the root"
		echo "      libraries directory at:"
		echo
		echo "      $PROJECT_DIR/libraries/lithium"
		echo
		echo "NOTE: Some errors are being surpressed in:"
		echo
		for FILE in $FILES; do
			echo "      $FILE"
		done
		echo

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
		TARGET=$PROJECT_DIR/admin/libraries/lithium

		echo "Removing old..."
		test -d $TARGET && rm -r $TARGET

		git clone git://github.com/UnionOfRAD/lithium.git $TARGET
		cd $TARGET
		git merge origin/data

		echo "Removing history..."
		rm -fr .git
		;;

	*)
		echo "Unknown command '${COMMAND}'."
		exit 1
		;;
esac

exit 0
