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

# Login information for your MySQL server.
# DB Tables are created during initial MySQL DB creation
# so point this to the same DB as your HLSX:CE install
DBHOST="localhost"
DBNAME="<YOUR_DB_NAME>"
DBUSER="<YOUR_DB_USER>"
DBPASS="<YOUR_DB_PASS>"

# Maximind API Key
API_KEY="<YOUR_API_KEY>"

#
# Nothing to change below here.
#

# Set API address and file name
API_URL="https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City-CSV&license_key=$API_KEY&suffix=zip"
FILE="GeoLiteCity-CSV.zip"

# Change to directory where installer is
cd `dirname $0`

# Error check for API key
if [[ $API_KEY =~ "<YOUR_API_KEY>" ]]; then
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

#Process blocks
echo "[>>] Processing Blocks"
mv GeoLite2-City-Blocks-IPv4.csv geoLite2_City_Blocks_IPv4.csv
echo "[<<] Processing complete"

#Import blocks to MySQL
echo "[>>] Importing Blocks to MySQL - THIS MIGHT TAKE SOME TIME!"
mysqlimport --fields-terminated-by=, --ignore-lines=1 --default-character-set=utf8 -L -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geoLite2_City_Blocks_IPv4.csv
echo "[<<] Import IPv4 complete"

#Process locations
echo "[>>] Processing locations"
mv GeoLite2-City-Locations-de.csv geoLite2_City_Location_de.csv
mv GeoLite2-City-Locations-en.csv geoLite2_City_Location_en.csv
mv GeoLite2-City-Locations-es.csv geoLite2_City_Location_es.csv
mv GeoLite2-City-Locations-fr.csv geoLite2_City_Location_fr.csv
mv GeoLite2-City-Locations-ja.csv geoLite2_City_Location_ja.csv
mv GeoLite2-City-Locations-pt-BR.csv geoLite2_City_Location_pt_BR.csv
mv GeoLite2-City-Locations-ru.csv geoLite2_City_Location_ru.csv
mv GeoLite2-City-Locations-zh-CN.csv geoLite2_City_Location_zh_CN.csv
echo "[<<] Processing complete"

#Import locations to MySQL
echo "[>>] Importing Locations to MySQL - THIS MIGHT TAKE SOME TIME!"
mysqlimport --fields-terminated-by=, --ignore-lines=1 --default-character-set=utf8 -L -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geoLite2_City_Location_de.csv
mysqlimport --fields-terminated-by=, --ignore-lines=1 --default-character-set=utf8 -L -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geoLite2_City_Location_en.csv
mysqlimport --fields-terminated-by=, --ignore-lines=1 --default-character-set=utf8 -L -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geoLite2_City_Location_es.csv
mysqlimport --fields-terminated-by=, --ignore-lines=1 --default-character-set=utf8 -L -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geoLite2_City_Location_fr.csv
mysqlimport --fields-terminated-by=, --ignore-lines=1 --default-character-set=utf8 -L -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geoLite2_City_Location_ja.csv
mysqlimport --fields-terminated-by=, --ignore-lines=1 --default-character-set=utf8 -L -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geoLite2_City_Location_pt_BR.csv
mysqlimport --fields-terminated-by=, --ignore-lines=1 --default-character-set=utf8 -L -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geoLite2_City_Location_ru.csv
mysqlimport --fields-terminated-by=, --ignore-lines=1 --default-character-set=utf8 -L -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geoLite2_City_Location_zh_CN.csv
echo "[<<] Importing complete"


# Cleanup
echo "[>>] Cleanup"
cd `dirname $0`
rm -f *.csv
rm -f *.txt
rm -f $FILE
echo "[<<] Cleanup complete"
