<?php
	// VOICECOMM MODULE
	global $db;
	
	define('TS', 0);
	define('VENT', 1);
	
	$result = $db->query("
		SELECT
			serverId,
			name,
			addr,
			password,
			descr,
			queryPort,
			UDPPort,
			serverType
		FROM
			hlstats_Servers_VoiceComm
        ");
  
	if ($db->num_rows($result) >= 1) {
		printSectionTitle('Voice Server');
?>
	<div class="subblock">
		<table class="data-table">
			<tr class="data-table-head">
				<td class="fSmall">Server Name</td>
				<td class="fSmall">Server Address</td>
				<td class="fSmall">Password</td>
				<td class="fSmall" style="text-align:right;">Channels</td>
				<td class="fSmall" style="text-align:right;">Slots&nbsp;used</td>
				<td class="fSmall">Notes</td>
			</tr> 
<?php
		$i = 0;
		$j = 0;
		while ($row = $db->fetch_array()) {
			if ($row['serverType'] == TS) {
				$ts_servers[$i]['serverId'] = $row['serverId'];
				$ts_servers[$i]['name'] = $row['name'];
				$ts_servers[$i]['addr'] = $row['addr'];
				$ts_servers[$i]['password'] = $row['password'];
				$ts_servers[$i]['descr'] = $row['descr'];
				$ts_servers[$i]['queryPort'] = $row['queryPort'];
				$ts_servers[$i]['UDPPort'] = $row['UDPPort'];
				$i++;
			} else if ($row['serverType'] == VENT) {
				$vent_servers[$j]['serverId'] = $row['serverId'];
				$vent_servers[$j]['name'] = $row['name'];
				$vent_servers[$j]['addr'] = $row['addr'];
				$vent_servers[$j]['password'] = $row['password'];
				$vent_servers[$j]['descr'] = $row['descr'];
				$vent_servers[$j]['queryPort'] = $row['queryPort'];
				$j++;
			}
		}
		if (isset($ts_servers))
		{
			require_once(PAGE_PATH . '/teamspeak_class.php');
			foreach($ts_servers as $ts_server)
			{
				$settings = $teamspeakDisplay->getDefaultSettings();
				$settings['serveraddress'] = $ts_server['addr'];
				$settings['serverqueryport'] = $ts_server['queryPort'];
				$settings['serverudpport'] = $ts_server['UDPPort'];
				$ts_info = $teamspeakDisplay->queryTeamspeakServerEx($settings);
				if ($ts_info['queryerror'] != 0) {
					$ts_channels = 'err';
					$ts_slots = $ts_info['queryerror'];
				} else {
					$ts_channels = count($ts_info['channellist']);
					$ts_slots = count($ts_info['playerlist']).'/'.$ts_info['serverinfo']['server_maxusers'];
				}
?>
        <tr class="bg1">
			<td class="fHeading">
				<img src="<?php echo IMAGE_PATH; ?>/teamspeak/teamspeak.gif" alt="tsicon" />
				&nbsp;<a href="<?php echo $g_options['scripturl'] . "?mode=teamspeak&amp;game=$game&amp;tsId=".$ts_server['serverId']; ?>"><?php echo trim($ts_server['name']); ?></a>
			</td>
			<td>
				<a href="teamspeak://<?php echo $ts_server['addr'].':'.$ts_server['UDPPort'] ?>/?channel=?password=<?php echo $ts_server['password']; ?>"><?php echo $ts_server['addr'].':'.$ts_server['UDPPort']; ?></a>
			</td>
			<td>
				<?php echo $ts_server['password']; ?>
			</td>
			<td style="text-align:right;">
				<?php echo $ts_channels; ?>
			</td>
			<td style="text-align:right;">
				<?php echo $ts_slots; ?>
			</td>
			<td>
				<?php echo $ts_server['descr']; ?>
			</td>
		</tr>
<?php
			}
		}
		if (isset($vent_servers))
		{
			require_once(PAGE_PATH . '/ventrilostatus.php');
			foreach($vent_servers as $vent_server)
			{
				$ve_info = new CVentriloStatus;
				$ve_info->m_cmdcode	= 2;					// Detail mode.
				$ve_info->m_cmdhost = $vent_server['addr'];
				$ve_info->m_cmdport = $vent_server['queryPort'];
				/////////
				$rc = $ve_info->Request();
			//	if ($rc) {
			//		echo "CVentriloStatus->Request() failed. <strong>$ve_info->m_error</strong><br /><br />\n";
			//	} else {
					$ve_channels = $ve_info->m_channelcount;
					$ve_slots = $ve_info->m_clientcount.'/'.$ve_info->m_maxclients;
			//	}
		?>  
			<tr class="bg1">
				<td class="fHeading">
					<img src="<?php echo IMAGE_PATH; ?>/ventrilo/ventrilo.png" alt="venticon" />
					&nbsp;<a href="<?php echo $g_options['scripturl'] . "?mode=ventrilo&amp;game=$game&amp;veId=".$vent_server['serverId']; ?>"><?php echo $vent_server['name']; ?></a>
				</td>
				<td>
					<a href="ventrilo://<?php echo $vent_server['addr'].':'.$vent_server['queryPort'] ?>/servername=<?php echo $ve_info->m_name; ?>">
					<?php echo $vent_server['addr'].':'.$vent_server['queryPort']; ?>
					</a></td>
				<td>
					<?php echo $vent_server['password']; ?>
				</td>
				<td style="text-align:right;">
					<?php echo $ve_channels; ?>
				</td>
				<td style="text-align:right;">
					<?php echo $ve_slots; ?>
				</td>
				<td>
					<?php echo $vent_server['descr']; ?>
				</td>
			</tr>
<?php
			}
		}
?>
    </table>
	</div>
<br /><br />
<?php
	}
	// VOICECOMM MODULE END
?>