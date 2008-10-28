#!/bin/bash

# ------------------------------------------------------------------------------
# Prints the log messages from the repository beginning from the revision
# number you passed. You may need your public key stored on the server.
#	
# get help message by using the terminal and perform this command:
# promt> ./viewHistory.sh --help
# display log messages
# promt> ./viewHistory.sh 20
# ------------------------------------------------------------------------------	
# @author Marcel Eichner // Ephigenia <love@ephigenia.de>
# @version 0.1
# ------------------------------------------------------------------------------

displayHelp() {
	echo '--------------------------------------------------------------------------------'
	echo ' viewHistory.sh'
	echo '--------------------------------------------------------------------------------'
	echo 'display the last log messages from the revision number you passed. You'
	echo 'may need to have your public ssh key stored on the server'
	echo ''
	echo 'usage: ./viewHistory.sh [revisionNumber]'
	echo ''
	echo 'Arguments: '
	echo ''
	echo '    $1   Revision number to use as start number'
	echo ''
	exit
}

# no arguments passed, or help requested, show usage message
if (test "$1" = '' || test "$1" = '-h' || test "$1" == '--help' || test "$1" == 'test') then
	displayHelp
fi

svn log -r $1:HEAD