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


# database is updated every first tuesday of any month, so download it with that specific date and import it
TODAY_MONTH=$( date +%m )
TODAY_YEAR=$( date +%Y )
if [ $LINUX_OTHER == "1" ]
 then CAL_COMMAND="cal -s" 
 else CAL_COMMAND="cal" 
fi
FIRST_TUESDAY_MONTH=$( $CAL_COMMAND $TODAY_MONTH $TODAY_YEAR |
 awk '
  NR == 1 { next }
  NR == 2 { next }
  NF <= 4 { next }
  NF == 5 { print $1 ; exit }
  NF == 6 { print $2 ; exit }
  NF == 7 { print $3 ; exit }
 ' )

DATE=""$TODAY_YEAR""$TODAY_MONTH"0"$FIRST_TUESDAY_MONTH"" 
DIR="GeoLiteCity_$DATE" 
FILE="GeoLiteCity_$DATE.zip" 
ls *.csv &>/dev/null && rm *.csv
[ -f $FILE ] || wget http://geolite.maxmind.com/download/geoip/database/GeoLiteCity_CSV/$FILE || exit 1
unzip -o $FILE || exit 1
mv $DIR/GeoLiteCity-Blocks.csv geoLiteCity_Blocks.csv
mv $DIR/GeoLiteCity-Location.csv geoLiteCity_Location.csv.temp
iconv -f ISO-8859-1 -t UTF-8 geoLiteCity_Location.csv.temp > geoLiteCity_Location.csv
mysqlimport -C -d --fields-terminated-by=, --fields-enclosed-by=\" --ignore-lines=2 --default-character-set=utf8 -L -i -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geoLiteCity_Blocks.csv 
mysqlimport -C -d --fields-terminated-by=, --fields-enclosed-by=\" --ignore-lines=2 --default-character-set=utf8 -L -i -h $DBHOST -u $DBUSER --password=$DBPASS $DBNAME geoLiteCity_Location.csv 

# Cleanup
ls *.csv &>/dev/null && rm *.csv
ls *.csv.temp &>/dev/null && rm *.csv.temp
rm $FILE
rmdir $DIR
