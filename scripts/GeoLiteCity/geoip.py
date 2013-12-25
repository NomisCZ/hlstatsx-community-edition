#!/usr/bin/env python
# HLstatsX Community Edition - Real-time player and clan rankings and statistics
# Copyleft (L) 2008-20XX Nicholas Hastings (nshastings@gmail.com)
# http://www.hlxcommunity.com

# HLstatsX Community Edition is a continuation of 
# ELstatsNEO - Real-time player and clan rankings and statistics
# Copyleft (L) 2008-20XX Malte Bayer (steam@neo-soft.org)
# http://ovrsized.neo-soft.org/

# ELstatsNEO is an very improved & enhanced - so called Ultra-Humongus Edition of HLstatsX
# HLstatsX - Real-time player and clan rankings and statistics for Half-Life 2
# http://www.hlstatsx.com/
# Copyright (C) 2005-2007 Tobias Oetzel (Tobi@hlstatsx.com)

# HLstatsX is an enhanced version of HLstats made by Simon Garner
# HLstats - Real-time player and clan rankings and statistics for Half-Life
# http://sourceforge.net/projects/hlstats/
# Copyright (C) 2001  Simon Garner
            
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.

# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.

# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

# For support and installation notes visit http://www.hlxcommunity.com


import os,sys

# version 1.0 alpha contributed to hlstatsx community by *XYZ*SaYnt

DBHOST="localhost"
DBNAME="your_hlstats_db"
DBUSER="your_sql_username"
DBPASS="your_sql_password"


def system(cmd):
    """
    executes a system call and returns the text as a string.  Returns only the first line of output.
    """
    print("EXECUTING: %s"%cmd)
    f = os.popen(cmd)
    output = f.readlines()
    f.close()
    if len(output) > 0:
        return output[0].replace("\n","")
    else:
        return ""


def fetch_geodata():
   """
   Obtains the geoLiteCity raw data, resulting in geoLiteCity_Location.csv and geoLiteCity_Blocks.csv
   """
   # database is updated on 1st every month, so download the file from the 1st of current month
   DAT  = system("date +%Y%m01")
   FIL  = "GeoLiteCity_%s"%DAT
   FILE = FIL + ".zip"
   system("rm *.csv > /dev/null")
   if not os.path.exists(FILE):
      system("wget http://geolite.maxmind.com/download/geoip/database/GeoLiteCity_CSV/" + FILE)
   system("unzip -o " + FILE)

   system("mv %s/GeoLiteCity-Blocks.csv geoLiteCity_Blocks.csv"%FIL)
   system("mv %s/GeoLiteCity-Location.csv geoLiteCity_Location.csv.temp"%FIL)
   system("rmdir " + FIL)
   system("iconv -f ISO-8859-1 -t UTF-8 geoLiteCity_Location.csv.temp > geoLiteCity_Location.csv")

   return


def dump_sql(fname):
   """
   Dump the new sql data into our database
   """
   system("mysql -u %s -p%s -h %s %s < %s"%(DBUSER,DBPASS,DBHOST,DBNAME,fname))  


def write_sql(fname):
   """
   Write a file of sql commands so that our data can be imported into our database.
   """
   try: 
      fout = open(fname,"w")
   except:   
      print("ERROR: unable to open "+fname)
      return 0
  
   fout.write("""
         DROP TABLE IF EXISTS `geoLiteCity_Blocks`;
         DROP TABLE IF EXISTS `geolitecity_blocks`;
         DROP TABLE IF EXISTS `geolitecity_location`;
         DROP TABLE IF EXISTS `geoLiteCity_Location`;

         CREATE TABLE `geoLiteCity_Blocks` 
         (`startIpNum` bigint(11) unsigned NOT NULL default '0',
         `endIpNum` bigint(11) unsigned NOT NULL default '0',
         `locId` bigint(11) unsigned NOT NULL default '0'
         ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

         CREATE TABLE `geoLiteCity_Location` (
         `locId` bigint(11) unsigned NOT NULL default '0',
         `country` varchar(2) NOT NULL,
         `region` varchar(50) default NULL,
         `city` varchar(50) default NULL,
         `postalCode` varchar(10) default NULL,
         `latitude` decimal(14,4) default NULL,
         `longitude` decimal(14,4) default NULL,
         PRIMARY KEY  (`locId`)
         ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   """)

   # read in the raw data  
   f = open("geoLiteCity_Blocks.csv")
   raw = f.readlines()
   f.close()
   del raw[0:2]

   # chunk up the raw data
   chunksize = 1000
   gblocks = []
   chunks = len(raw)/chunksize + 1
   for i in xrange(0,chunks): gblocks.append(raw[i*chunksize:(i+1)*chunksize])
   print("SQL: %d data items, %d chunks"%(len(raw),chunks))
   del raw

   for chunk in gblocks:
      if chunk:
         s = "insert into `geoLiteCity_Blocks`(`startIpNum`,`endIpNum`,`locId`) values"
         for l in chunk:
            vals = l.replace('"',"").replace('\n',"").split(',')
            s += "(%s,%s,%s),"%(vals[0],vals[1],vals[2])
         s = s[0:-1] # chop the last comma
         fout.write(s + ";\n");
   del gblocks




   f = open("geoLiteCity_Location.csv")
   raw = f.readlines()
   f.close()
   del raw[0:2]

   # chunk up the raw data
   gblocks = []
   chunks = len(raw)/chunksize + 1
   for i in xrange(0,chunks): gblocks.append(raw[i*chunksize:(i+1)*chunksize])
   print("SQL: %d data items, %d chunks"%(len(raw),chunks))
   del raw

   for chunk in gblocks:
      if chunk:
         s = "insert into `geoLiteCity_Location`(`locId`,`country`,`region`,`city`,`postalCode`,`latitude`,`longitude`) values" 
         for l in chunk:
            vals = l.replace('"',"").replace('\n',"").split(',')
            for i,v in enumerate(vals): vals[i] = v.replace("'","\\'")
            s += "(%s,'%s','%s','%s','%s','%s','%s'),"%(vals[0],vals[1],vals[2],vals[3],vals[4],vals[5],vals[6])
         s = s[0:-1] # chop the last comma
         fout.write(s + ";\n");
   del gblocks

   return 1

   
def main():
   sqlname = "geodata.sql"
   print("DOWNLOADING GEO DATA....")
   fetch_geodata()
   print("WRITING DATABASE FILE....")
   if write_sql(sqlname):
      print("IMPORTING DATABASE FILE....")
      dump_sql(sqlname)
   else:
      print("Fatal error; unable to finish.") 

   # clean up.
   system("rm "+sqlname)

   print("DONE.")



main()

