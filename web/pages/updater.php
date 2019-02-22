<?php

	if ( !defined('IN_HLSTATS') )
	{
		die('Do not access this file directly.');
	}
	
	if ( !file_exists("./updater") )
	{
		die('Updater directory is missing.');
	}
	
	define('IN_UPDATER', true);
	
	pageHeader
	(
		array ($gamename, 'Updater')
	);
	echo "<div class=\"warning\">\n" .
	"<span id=\"warning-header\"><strong>HLX:CE Database Updater log</span></strong><br /><br />\n";
	// Check version since updater wasn't implemented until version 1.6.2
	$versioncomp = version_compare($g_options['version'], '1.6.1');
	
	if ($versioncomp === -1)
	{
		// not yet at 1.6.1
		echo "You cannot upgrade from this version (".$g_options['version']."). You can only upgrade from 1.6.1.  Please manually apply the SQL updates found in the SQL folder through 1.6.1, then re-run this updater.\n";
	}
	else if ($versioncomp === 0)
	{
		// at 1.6.1, up to 1.6.2
		include ("./updater/update161-162.php");		
	}
	else
	{
		// at 1.6.2 or higher, can update normally
		echo "Currently on database version ".$g_options['dbversion']."<br />\n";
		$i = $g_options['dbversion']+1;
		
		while (file_exists ("./updater/$i.php"))
		{
			echo "<br /><em>Running database update $i</em><br />\n";
			include ("./updater/$i.php");
			
			echo "<em>Database update for DB Version $i complete.</em><br />";
			$i++;
			
		}
		
		if ($i == $g_options['dbversion']+1)
		{
			echo "<strong>Your database is already up to date (".$g_options['dbversion'].")</strong>\n";
		}
		else
		{
			echo "<br /><strong>Successfully updated to database version ".($i-1)."!</strong>\n";
		}
	}
	
	echo "<br /><br /><img src=\"".IMAGE_PATH."/warning.gif\" alt=\"Warning\"> <span class=\"warning-header\">You <strong>must delete</strong> the \"updater\" folder from your web site before your site will be operational.</span>\n</div>\n";
?>