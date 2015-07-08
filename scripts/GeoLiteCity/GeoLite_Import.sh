#!/bin/sh
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

# Set this value to 1 if you are running Gentoo linux, or any other linux distro where the "cal" command outputs not Sunday as the first day in every row!
LINUX_OTHER="0" 

# Login information for your MySQL server
DBHOST="localhost"
DBNAME=""
DBUSER=""
DBPASS=""

#
# Nothing to change below here.
#


FILE="GeoLiteCity-latest.zip"
rm -f geoLiteCity_Blocks.csv
rm -f geoLiteCity_Location.csv
rm -f geoLiteCity_Location.csv.temp
rm -f $FILE
[ -f $FILE ] || wget http://geolite.maxmind.com/download/geoip/database/GeoLiteCity_CSV/$FILE || exit 1
unzip -jo $FILE || exit 1
mv GeoLiteCity-Blocks.csv geoLiteCity_Blocks.csv
mv GeoLiteCity-Location.csv geoLiteCity_Location.csv.temp
iconv -f ISO-8859-1 -t UTF-8 geoLiteCity_Location.csv.temp > geoLiteCity_Location.csv
mysqlimport -C -d --fields-terminated-by=, --fields-enclosed-by=\" --ignore-lines=2 --default-character-set=utf8 -L -i -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geoLiteCity_Blocks.csv 
mysqlimport -C -d --fields-terminated-by=, --fields-enclosed-by=\" --ignore-lines=2 --default-character-set=utf8 -L -i -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geoLiteCity_Location.csv 

# Cleanup
rm -f geoLiteCity_Blocks.csv
rm -f geoLiteCity_Location.csv
rm -f geoLiteCity_Location.csv.temp
rm -f $FILE
