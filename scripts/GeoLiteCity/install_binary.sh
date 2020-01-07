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

API_KEY="<YOUR_API_KEY>"

# ***** NOTHING TO CONFIGURE BELOW HERE *****

API_URL="https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key=$API_KEY&suffix=tar.gz"

FILE=GeoLite2-City
FILE_EXT=.tar.gz

# Change to directory where installer is
cd `dirname $0`

if [[ $API_KEY =~ "<YOUR_API_KEY>" ]]; then
  echo "----------------------------------------------------------"
  echo "[!] You probably forgot to set yours MaxMind account API key!"
  echo "[i] Please check installation instructions > 2.4. Prepare GeoIP2 (optional) > https://github.com/NomisCZ/hlstatsx-community-edition/wiki/Installation#2-installation"
  echo "----------------------------------------------------------"
  exit 3
fi

echo "[>>] Downloading GeoLite2-City database"
wget -N -q $API_URL -O $FILE$FILE_EXT

echo "[<<] Uncompressing $FILE$FILE_EXT"
tar -zxvf $FILE$FILE_EXT

echo "[->] Moving $FILE.mmdb file to $PWD"
mv ./${FILE}_*/${FILE}.mmdb ./
rm -R ./${FILE}_*
rm $FILE$FILE_EXT

chmod 777 GeoLite2-City.mmdb
echo "[✓] Done"
