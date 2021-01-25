#!/bin/bash
# HLstatsX Community Edition - Real-time player and clan rankings and statistics
# Copyleft (L) 2008-20XX Nicholas Hastings (nshastings@gmail.com)
# http://www.hlxcommunity.com
#
# HLstatsX Community Edition is a continuation of
# ELstatsNEO - Real-time player and clan rankings and statistics
# Copyleft (L) 2008-20XX Malte Bayer (steam@neo-soft.org)
# http://ovrsized.neo-soft.org/
#
# ELstatsNEO is an very improved & enhanced - so called Ultra-Humongus Edition of HLstatsX
# HLstatsX - Real-time player and clan rankings and statistics for Half-Life 2
# http://www.hlstatsx.com/
# Copyright (C) 2005-2007 Tobias Oetzel (Tobi@hlstatsx.com)
#
# HLstatsX is an enhanced version of HLstats made by Simon Garner
# HLstats - Real-time player and clan rankings and statistics for Half-Life
# http://sourceforge.net/projects/hlstats/
# Copyright (C) 2001  Simon Garner
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
#
# For support and installation notes visit http://www.hlxcommunity.com

# Configure the variables below 

#Login information for your MySQL server
DBHOST="localhost"
DBNAME="<DBNAMEHERE>"
DBUSER="<DBUSERHERE>"
DBPASS="<DBPASSHERE>"

#API Key
API_KEY="<MAXMINDAPIKEYHERE>"

#----------------------------
#Nothing to change below here.
#----------------------------

API_URL="https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City-CSV&license_key=$API_KEY&suffix=zip"

FILE="GeoLiteCity-CSV.zip"

#Change to directory where installer is
cd `dirname $0`

#Error check for API Key
if [[ $API_KEY =~ "<MAXMINDAPIKEYHERE>" ]]; then
  echo "----------------------------------------------------------"
  echo "[!] You probably forgot to set yours MaxMind account API key!"
  echo "[i] Please check installation instructions > 2.4. Prepare GeoIP2 (optional) > https://github.com/NomisCZ/hlstatsx-community-edition/wiki/Installation#2-installation"
  echo "----------------------------------------------------------"
  exit 3
fi

#Download zip
echo "[>>] Downloading GeoLite2-City database"
wget -N -q $API_URL -O $FILE
echo "[<<] Download complete"

#Decompress zip
echo "[>>] Decompressing $FILE$FILE_EXT"
unzip -jo $FILE
echo "[<<] Decompress complete"

#Rename blocks
echo "[>>] Renaming Files"
mv GeoLite2-City-Blocks-IPv4.csv geolitecity_blocks-4.csv
mv GeoLite2-City-Blocks-IPv6.csv geolitecity_blocks-6.csv
echo "[<<] Renaming complete"

#Import blocks to MySQL
echo "[>>] Importing Blocks to MySQL - THIS MIGHT TAKE SOME TIME!"
mysqlimport -C -d --fields-terminated-by=, --fields-enclosed-by=\" --ignore-lines=2 --default-character-set=utf8 -L -i -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geolitecity_blocks-4.csv
echo "[<<] Import IPv4 complete"
mysqlimport -C -d --fields-terminated-by=, --fields-enclosed-by=\" --ignore-lines=2 --default-character-set=utf8 -L -i -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geolitecity_blocks-6.csv
echo "[<<] Import IPv6 complete"

#Process locations
echo "[>>] Converting locations"
mv GeoLite2-City-Locations-de.csv geolitecity_location-de.csv.temp
iconv -f ISO-8859-1 -t UTF-8 geolitecity_location-de.csv.temp > geolitecity_location-de.csv
mv GeoLite2-City-Locations-en.csv geolitecity_location-en.csv.temp
iconv -f ISO-8859-1 -t UTF-8 geolitecity_location-en.csv.temp > geolitecity_location-en.csv
mv GeoLite2-City-Locations-es.csv geolitecity_location-es.csv.temp
iconv -f ISO-8859-1 -t UTF-8 geolitecity_location-es.csv.temp > geolitecity_location-es.csv
mv GeoLite2-City-Locations-fr.csv geolitecity_location-fr.csv.temp
iconv -f ISO-8859-1 -t UTF-8 geolitecity_location-fr.csv.temp > geolitecity_location-fr.csv
mv GeoLite2-City-Locations-ja.csv geolitecity_location-ja.csv.temp
iconv -f ISO-8859-1 -t UTF-8 geolitecity_location-ja.csv.temp > geolitecity_location-ja.csv
mv GeoLite2-City-Locations-pt-BR.csv geolitecity_location-pt-br.csv.temp
iconv -f ISO-8859-1 -t UTF-8 geolitecity_location-pt-br.csv.temp > geolitecity_location-pt-br.csv
mv GeoLite2-City-Locations-ru.csv geolitecity_location-ru.csv.temp
iconv -f ISO-8859-1 -t UTF-8 geolitecity_location-ru.csv.temp > geolitecity_location-ru.csv
mv GeoLite2-City-Locations-zh-CN.csv geolitecity_location-zh-cn.csv.temp
iconv -f ISO-8859-1 -t UTF-8 geolitecity_location-zh-cn.csv.temp > geolitecity_location-zh-cn.csv
echo "[<<] Processing complete"

#Import blocks to MySQL
echo "[>>] Importing Locations to MySQL - THIS MIGHT TAKE SOME TIME!"
mysqlimport -C -d --fields-terminated-by=, --fields-enclosed-by=\" --ignore-lines=2 --default-character-set=utf8 -L -i -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geolitecity_location-de.csv
mysqlimport -C -d --fields-terminated-by=, --fields-enclosed-by=\" --ignore-lines=2 --default-character-set=utf8 -L -i -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geolitecity_location-en.csv
mysqlimport -C -d --fields-terminated-by=, --fields-enclosed-by=\" --ignore-lines=2 --default-character-set=utf8 -L -i -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geolitecity_location-es.csv
mysqlimport -C -d --fields-terminated-by=, --fields-enclosed-by=\" --ignore-lines=2 --default-character-set=utf8 -L -i -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geolitecity_location-fr.csv
mysqlimport -C -d --fields-terminated-by=, --fields-enclosed-by=\" --ignore-lines=2 --default-character-set=utf8 -L -i -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geolitecity_location-ja.csv
mysqlimport -C -d --fields-terminated-by=, --fields-enclosed-by=\" --ignore-lines=2 --default-character-set=utf8 -L -i -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geolitecity_location-pt-br.csv
mysqlimport -C -d --fields-terminated-by=, --fields-enclosed-by=\" --ignore-lines=2 --default-character-set=utf8 -L -i -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geolitecity_location-ru.csv
mysqlimport -C -d --fields-terminated-by=, --fields-enclosed-by=\" --ignore-lines=2 --default-character-set=utf8 -L -i -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geolitecity_location-zh-cn.csv
echo "[<<] Importing complete"


# Cleanup
echo "[>>] Cleanup"
rm -f geolitecity_blocks-4.csv
rm -f geolitecity_blocks-6.csv
rm -f geolitecity_location-de.csv.temp
rm -f geolitecity_location-en.csv.temp
rm -f geolitecity_location-es.csv.temp
rm -f geolitecity_location-fr.csv.temp
rm -f geolitecity_location-ja.csv.temp
rm -f geolitecity_location-pt-br.csv.temp
rm -f geolitecity_location-ru.csv.temp
rm -f geolitecity_location-zh-cn.csv.temp
rm -f geolitecity_location-de.csv
rm -f geolitecity_location-en.csv
rm -f geolitecity_location-es.csv
rm -f geolitecity_location-fr.csv
rm -f geolitecity_location-ja.csv
rm -f geolitecity_location-pt-br.csv
rm -f geolitecity_location-ru.csv
rm -f geolitecity_location-zh-cn.csv
rm -f $FILE
echo "[<<] Cleanup complete"
