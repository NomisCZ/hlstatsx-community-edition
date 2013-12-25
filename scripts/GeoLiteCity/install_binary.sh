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

# *** You should not need to configure anything in this script. ***
# URL is the location to download the GeoLiteCity.dat.gz file from.
URL=http://geolite.maxmind.com/download/geoip/database/

# File is the file to download from the server above, as well as what to store it as locally.
FILE=GeoLiteCity.dat.gz

# ***** NOTHING TO CONFIGURE BELOW HERE *****

# Change to directory where installer is
cd `dirname $0`

echo Downloading a new copy of GeoLiteCity, if needed.
wget -N -q $URL$FILE
echo Uncompressing database
gzip -dc < $FILE > $(basename $FILE .gz)
chmod 777 *.dat
echo Done
