#!/bin/bash
#
# Collapsible Categories extension for the phpBB Forum Software package.
#
# @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
# @license GNU General Public License, version 2 (GPL-2.0)
#
set -e
set -x

DB=$1
TRAVIS_PHP_VERSION=$2
EXTNAME=$3

if [ "$TRAVIS_PHP_VERSION" == "7.2" ] && [ "$DB" == "mysqli" ]
then
	sed -n '1h;1!H;${;g;s/<\/php>/<\/php>\n\t<filter>\n\t\t<whitelist>\n\t\t\t<directory>..\/<\/directory>\n\t\t\t<exclude>\n\t\t\t\t<directory>..\/tests\/<\/directory>\n\t\t\t\t<directory>..\/core\/language\/<\/directory>\n\t\t\t\t<directory>..\/core\/migrations\/<\/directory>\n\t\t\t\t<directory>..\/acpcleanup\/migrations\/<\/directory>\n\t\t\t\t<directory>..\/acpcleanup\/language\/<\/directory>\n\t\t\t\t<directory>..\/acpimport\/migrations\/<\/directory>\n\t\t\t\t<directory>..\/acpimport\/language\/<\/directory>\n\t\t\t\t<directory>..\/exif\/migrations\/<\/directory>\n\t\t\t\t<directory>..\/exif\/language\/<\/directory>\n\t\t\t<\/exclude>\n\t\t<\/whitelist>\n\t<\/filter>/g;p;}' phpBB/ext/"$EXTNAME"/travis/phpunit-mysqli-travis.xml &> phpBB/ext/"$EXTNAME"/travis/phpunit-mysqli-travis.xml.bak
	cp phpBB/ext/"$EXTNAME"/travis/phpunit-mysqli-travis.xml.bak phpBB/ext/"$EXTNAME"/travis/phpunit-mysqli-travis.xml
fi
