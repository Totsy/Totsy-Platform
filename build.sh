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
	echo " - run-app-tests     Runs app tests."
	echo " - optimize-repo     Perform GC on local git repository."
	echo " - source-subs       Initialize and update all submodules."
	echo " - source-pear       Install symlink to PEAR."
	echo " - source-selenium   Install dependencies."
	echo " - clear-cache       Clears file caches on admin and app."
	echo " - selenium-server   Start the selenium server."
	echo " - analyze           Run static code analysis tools."
	echo " - optimize-imgs     Run all images through optimizers, reducing size."
	echo " - optimize-js       Run all JS through optimizers, reducing size."
	echo " - versions          Output versions."
	echo " - bench-static      Run siege bench for static resources."
	echo " - bench-dynamic     Run siege bench for dynamic resources."
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
		$0 source-pear

		read -p "Do you want selenium support? (y/n) " CONFIRM
		if [[ $CONFIRM == "y" ]]; then
			$0 source-selenium
		fi

		$0 fix-perms

		FILES=$(find $PROJECT_DIR/{app,admin} -type f -print0 | xargs -0 grep -l -i -E 'ini_set.*display_error.*(off|false|0)')

		echo
		echo
		echo "QA: Some errors are being surpressed in:"
		echo
		for FILE in $FILES; do
			echo "      $FILE"
		done
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


	run-app-tests)
		cd $PROJECT_DIR/app
		libraries/lithium/console/li3 test tests/cases
		libraries/lithium/console/li3 test ../libraries/li3_payments/tests

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

	source-pear)
		PEAR=$(pear config-show  | grep php_dir | awk '{ print $4 }')

		echo "Symlinking in PEAR from $PEAR..."
		test -L $PROJECT_DIR/libraries/PEAR && rm $PROJECT_DIR/libraries/PEAR
		ln -s $PEAR $PROJECT_DIR/libraries/PEAR
		;;

	source-selenium)
		echo "Installing pear package..."
		pear install Testing_Selenium-alpha

		echo "Downloading server packages..."
		curl http://selenium.googlecode.com/files/selenium-server-standalone-2.2.0.jar \
			--O $PROJECT_DIR/selenium/server.jar
		;;

	clear-cache)
		find $PROJECT_DIR/app/resources/tmp/cache -name 'empty' -prune -o -type f | xargs rm -v
		find $PROJECT_DIR/admin/resources/tmp/cache -name 'empty' -prune -o -type f | xargs rm -v
		;;

	selenium-server)
		echo "NOTE: If firefox doesn't start correctly on OSX execute the following steps."
		echo
		echo "----------------------------------------------------------------------------"
		echo 'cd /Applications/Firefox.app/Contents/MacOS'
		echo 'mv firefox-bin firefox-bin.original'
		echo 'ditto --arch i386 firefox-bin.original firefox-bin'
		echo "----------------------------------------------------------------------------"
		echo

		java \
			-jar $PROJECT_DIR/selenium/server.jar \
			-firefoxProfileTemplate $PROJECT_DIR/selenium/tzp8knyf.selenium \
			-log $PROJECT_DIR/selenium/selenium.log \
			-browserSideLog
		;;

	analyze)
		cd $PROJECT_DIR
		phploc --exclude app/libraries --exclude app/tests app libraries/totsy_common
		phpcpd --exclude app/libraries --exclude app/tests app libraries/totsy_common
		# phpmd --exclude app/libraries,app/tests app,libraries/totsy_common text codesize,naming
		;;

	optimize-imgs)
		cd $PROJECT_DIR
		du -s app/webroot/img

		for FILE in $(find app/webroot/img -name "*.png"); do
			BEFORE=$(ls -lah $FILE | awk '{ print $5 }')

			pngcrush -rem alla -rem text -q $FILE $FILE.tmp
			mv $FILE.tmp $FILE

			AFTER=$(ls -lah $FILE | awk '{ print $5 }')
			echo "$FILE ($BEFORE -> $AFTER)"
		done
		for FILE in $(find app/webroot/img -name "*.jpg"); do
			BEFORE=$(ls -lah $FILE | awk '{ print $5 }')

			jpegtran -optimize -copy none $FILE > $FILE.tmp
			mv $FILE.tmp $FILE

			AFTER=$(ls -lah $FILE | awk '{ print $5 }')
			echo "$FILE ($BEFORE -> $AFTER)"
		done

		du -s app/webroot/img
		;;

	optimize-js)
		cd $PROJECT_DIR
		du -s app/webroot/js

		YUICOMPRESSOR="java -jar $HOME/Scripts/yuicompressor.jar"

		SKIP='.*(analytics.*)'
		for FILE in $(find -E app/webroot/js -regex $SKIP -prune -o -name "*.js" -print); do
			BEFORE=$(ls -lah $FILE | awk '{ print $5 }')

			$YUICOMPRESSOR -o $FILE --nomunge --charset utf-8 $FILE

			AFTER=$(ls -lah $FILE | awk '{ print $5 }')
			echo "$FILE ($BEFORE -> $AFTER)"
		done

		du -s app/webroot/js
		;;

	versions)
		cd $PROJECT_DIR
		echo "rev: $(git rev-parse --short HEAD)"
		echo "tag: $(git tag | tail -n 1)"
		;;

	bench-static)
		echo "
http://totsy/css/base.css
http://totsy/img/logo.png
http://totsy/img/login/1567614_new.jpg
http://totsy/js/jquery.backstretch.min.js
" > /tmp/urls.txt
		siege -r 50000 -c 1 -b -f /tmp/urls.txt
		;;

	bench-dynamic)
		# You must keep this cookie current.
		COOKIE="appcookie[cookieCrumb][landing_url]=%2Fregister; appcookie[cookieCrumb][entryTime]=1326120662; __utma=21492158.107962085.1326120716.1326120716.1326214372.2; __utmz=21492158.1326120716.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); appcookie[cookieCrumb][user_id]=4ecbe40fed08e0760b000018; PHPSESSID=sce93eeh954oei66cei0t0lq55; __utmb=21492158.4.10.1326214372; __utmc=21492158"
		echo "
http://totsy/login
http://totsy/register
http://totsy/sales
http://totsy/sale/dinkers-the-monkey
http://totsy/cart/view
http://totsy/account
http://totsy/pages/terms
" > /tmp/urls.txt
		siege -r 100 -c 2 -b --header="Cookie: $COOKIE" -f /tmp/urls.txt
		;;

	*)
		echo "Unknown command '${COMMAND}'."
		exit 1
		;;
esac

exit 0
