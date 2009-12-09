#!/bin/bash

# ------------------------------------------------------------------------------
# This file can help you create a new application base on the ephFrame framework
#
# get help message by using the terminal and perform this command:
# promt> ./createApp.sh --help
# ------------------------------------------------------------------------------	
# @author Marcel Eichner // Ephigenia <love@ephigenia.de>
# @version 0.1
# ------------------------------------------------------------------------------

# configuration
svndsn='export svn+ssh://moresleep.net/home/51916/data/ephFrame/0.2/app/';

displayHelp() {
	echo 'this can help you creating a new simple project file structure for a ephFrame'
	echo 'based application by exporting the current stable release from ephFrame app to'
	echo 'the target you have speicified.'
	echo ''
	echo 'usage: ./createApp.sh [target]'
	echo ''
	echo 'Arguments: '
	echo ''
	echo '    $1   The target directory where the app should be created.'
	echo '         If will be created if it does not exist. If it exists you’ll'
	echo '         have to confirm the creation.'
	echo ''
	exit
}

# hello message
echo '--------------------------------------------------------------------------------'
echo ' createApp.sh'
echo '--------------------------------------------------------------------------------'
echo ''

# no arguments passed, or help requested, show usage message
if (test "$1" = '' || test "$1" = '-h' || test "$1" == '--help' || test "$1" == 'test') then
	displayHelp
fi

target="$1"

# test if target directory exists
if (test ! -d "$target") then
	mkdir $target
	if (test ! -d "$target") then
		echo 'Failed creating directory, please check file permissions!'
		exit
	fi
	command="svn $svndsn $target"
else
	echo 'Target directory allready exists. Some files may be overwritten during export.'
	printf 'Please verify your target directory by pressing (y) or (n): '
	read -n 1 result
	if [ "$result" != 'y' ]; then
		echo ''
		echo '... okay, then i’ll stop now. bye!'
		exit
	fi
	command="svn --force $svndsn $target"	
fi

# perform final svn export action
echo ''
echo "start exporting subversion app project from ephFrame Repository to:"
echo "$target ..."
echo ''
$command

echo ''
echo "done!, now you can start creating your own ephFrame Application in '$target'"
echo ''