<?php
	pageHeader(
		array('Teamspeak viewer'),
		array('Teamspeak viewer' => '')
	);
  include (PAGE_PATH.'/voicecomm_serverlist.php');
  include (PAGE_PATH.'/teamspeak_query.php');

  $tsId = valid_request($_GET['tsId'],1);


function show($tpl, $array)
{
    $template = PAGE_PATH."/templates/teamspeak/$tpl";
  
    if($fp = @fopen($template.".".html, "r"))
      $tpl = @fread($fp, filesize($template.".".html));
    
    foreach($array as $value => $code)
    {
      $tpl = str_replace("[".$value."]", $code, $tpl);
    }
  return $tpl;
}



  if(function_exists(fopen))
  {
    $db->query("SELECT addr, queryPort, UDPPort FROM hlstats_Servers_VoiceComm WHERE serverId=$tsId");
    $s = $db->fetch_array();

    $uip 	= $s['addr'];
    $tPort 	= $s['queryPort'];
    $port 	= $s['UDPPort'];

  	$fp = fsockopen($uip, $tPort, $errno, $errstr, 2);

	  if(!$fp)
    {
      $index = error("No teamspeak", 1);
    } else {
	    $out = "";
	    $fp = fsockopen($uip, $tPort, $errno, $errstr, 2);
	    if($fp)
      {
		    fputs($fp, "sel $port\n");
		    fputs($fp, "si\n");
		    fputs($fp, "quit\n");
		    while(!feof($fp))
        {
			    $out .= fgets($fp, 1024);
		    }
		    $out = str_replace('[TS]', '', $out);
		    $out = str_replace('OK', '', $out);
		    $out = trim($out);

  		$name=substr($out,indexOf($out,'server_name='),strlen($out));
	  	$name=substr($name,0,indexOf($name,'server_platform=')-strlen('server_platform='));
		  $os=substr($out,indexOf($out,'server_platform='),strlen($out));
		  $os=substr($os,0,indexOf($os,'server_welcomemessage=')-strlen('server_welcomemessage='));
		  $uptime=substr($out,indexOf($out,'server_uptime='),strlen($out));
		  $uptime=substr($uptime,0,indexOf($uptime,'server_currrentusers=')-strlen('server_currrentusers='));
		  $cAmount=substr($out,indexOf($out,'server_currentchannels='),strlen($out));
		  $cAmount=substr($cAmount,0,indexOf($cAmount,'server_bwinlastsec=')-strlen('server_bwinlastsec='));
		  $user=substr($out,indexOf($out,'server_currentusers='),strlen($out));
		  $user=substr($user,0,indexOf($user,'server_currentchannels=')-strlen('server_currentchannels='));
		  $max=substr($out,indexOf($out,'server_maxusers='),strlen($out));
		  $max=substr($max,0,indexOf($max,'server_allow_codec_celp51=')-strlen('server_allow_codec_celp51='));
      fclose($fp);
  	}

  	$uArray = array();
	  $innerArray = array();
	  $out = "";
	  $j = 0;
	  $k = 0;

  	$fp = fsockopen($uip, $tPort, $errno, $errstr, 30);
	  if($fp)
    {
		  fputs($fp, "pl ".$port."\n");
		  fputs($fp, "quit\n");
		  while(!feof($fp))
      {
			  $out .= fgets($fp, 1024);
		  }
		  $out = str_replace('[TS]', '', $out);
		  $out = str_replace('loginname', "loginname\t", $out);
		  $data	= explode("\t", $out);

		  for($i=0;$i<count($data);$i++)
      {
			  $innerArray[$j] = $data[$i];
			  if($j>=15)
			  {
				  $uArray[$k]=$innerArray;
				  $j = 0;
				  $k = $k+1;
			  } else {
				  $j++;
			  }
		  }
		  fclose($fp);
	  }
	  $debug = false;

    for($i=1;$i<count($uArray);$i++)
    {
	    $innerArray=$uArray[$i];
      $p = setUserStatus($innerArray[12])."&nbsp;<span style=\"font-weight:bold;\">".removeChar($innerArray[14])."</span>
           &nbsp;(".setPPriv($innerArray[11])."".setCPriv($innerArray[10]).")";
           
      $class = ($color % 2) ? "bg2" : "bg1"; $color++;
      $userstats .= show("/userstats", array("player" => $p,
                                                  "channel" => getChannelName($innerArray[1],$uip,$port,$tPort),
                                                  "misc1" => $innerArray[6],
                                                  "class" => $class,
                                                  "misc2" => $innerArray[7],
                                                  "misc3" => time_convert($innerArray[8]),
                                                  "misc4" => time_convert($innerArray[9])));

	  }

  	$uArr = getTSChannelUsers($uip,$port,$tPort);
	  $pcArr = Array();
	  $ccArr = Array();
	  $thisArr = Array();
	  $listArr = Array();
	  $usedArr = Array();
	  $cArr	= getChannels($uip,$port,$tPort);
	  $z = 0;
	  $x = 0;

  	for($i=0;$i<count($cArr);$i++)
	  {
		  $innerArr=$cArr[$i];
		  $listArr[$i]=$innerArr[3];
	  }
	  sort($listArr);
	  for($i=0;$i<count($listArr);$i++)
	  {
		  for($j=0;$j<count($cArr);$j++)
		  {
			  $innArr=$cArr[$j];

			  if($innArr[3]==$listArr[$i] && usedID($usedArr,$innArr[0]))
			  {
				  if($innArr[2]==-1)
				  {
					  $thisArr[0] = $innArr[0];
					  $thisArr[1] = $innArr[5];
					  $thisArr[2] = $innArr[2];
					  $pcArr[$z] = $thisArr;
					  $usedArr[count($usedArr)] = $innArr[0];
					  $z++;
				  } else {
					  $thisArr[0] = $innArr[0];
					  $thisArr[1] = $innArr[5];
					  $thisArr[2] = $innArr[2];
					  $ccArr[$x] = $thisArr;
					  $usedArr[count($usedArr)] = $innArr[0];
					  $x++;
				  }
			  }
		  }
	  }

	  for($i=0;$i<count($pcArr);$i++)
    {
	    $innerArr=$pcArr[$i];

      $subchan = "";
	    for($j=0;$j<count($ccArr);$j++)
      {
	      $innerCCArray=$ccArr[$j];
	      if($innerArr[0]==$innerCCArray[2])
        {
   	      for($p=1;$p<count($uArr);$p++)
          {
            $subusers = "";
            for($p=1;$p<count($uArr);$p++)
            {
			        $innerUArray=$uArr[$p];
			        if($innerCCArray[0]==$innerUArray[1])
			        {
                $subusers .= "&nbsp;&nbsp;&nbsp;&nbsp;<img src=\"".IMAGE_PATH."/teamspeak/trenner.gif\" alt=\"\" class=\"tsicon\" />".setUserStatus($innerUArray[12])."&nbsp;<span style=\"font-weight:bold;\">".removeChar($innerUArray[14])."</span>&nbsp;(".setPPriv($innerUArray[11])."".setCPriv($innerUArray[10]).")<br />";
	            }
		        }
		      }
          $subchannels = "<img src=\"".IMAGE_PATH."/teamspeak/trenner.gif\" alt=\"\" class=\"tsicon\" /><img src=\"".IMAGE_PATH."/teamspeak/channel.gif\" alt=\"\" class=\"tsicon\" /><a style=\"font-weight:normal\" href=\"hlstats.php?mode=teamspeak&amp;game=$game&amp;tsId=$tsId&amp;cID=".$innerCCArray[0]."&amp;type=1\">&nbsp;".removeChar($innerCCArray[1])."&nbsp;</a><br /> ".$subusers."";
          $subchan .= show("subchannels", array("subchannels" => $subchannels));
	      }
      }
      $users = "";
      for($k=1;$k<count($uArr);$k++)
      {
		    $innerUArray=$uArr[$k];
		    if($innerArr[0]==$innerUArray[1])
        {
          $users .= "<img src=\"".IMAGE_PATH."/teamspeak/trenner.gif\" alt=\"\" class=\"tsicon\" />".setUserStatus($innerUArray[12])."<span style=\"font-weight:bold;\">".removeChar($innerUArray[14])."</span>&nbsp;(".setPPriv($innerUArray[11])."".setCPriv($innerUArray[10]).")<br />";
		    }
	    }

      $channels = "<img src=\"".IMAGE_PATH."/teamspeak/channel.gif\" alt=\"\" class=\"tsicon\" />&nbsp;<a style=\"font-weight:bold\" href=\"hlstats.php?mode=teamspeak&amp;game=$game&amp;tsId=$tsId&amp;cID=".trim($innerArr[0])."&amp;type=1\">".removeChar($innerArr[1])."&nbsp;</a><br /> ".$users."";

      $chan .= show("channel", array("channel" => $channels,
                                           "subchannels" => $subchan));

    }

    if(isset($_GET['cID']))
    {
	    $cID 	= $_GET['cID'];
	    $type	= $_GET['type'];
    } else {
	    $cID 	= 0;
	    $type	= 0;
    }

    if($type==0)     $info = defaultInfo($uip,$tPort,$port);
    elseif($type==1) $info = channelInfo($uip,$tPort,$port,$cID);

    $outp_str = show("teamspeak", array("name" => $name,
                                           "os" => $os,
                                           "uptime" => time_convert($uptime),
                                           "user" => $user,
                                           "t_name" => "Server name",
                                           "t_os" => "Operating system",
                                           "uchannels" => $chan,
                                           "info" => $info,
                                           "t_uptime" => "Uptime",
                                           "t_channels" => "Channels",
                                           "t_user" => "Users",
                                           "head" => "Teamspeak Overview",
                                           "users_head" => "User Information",
                                           "player" => "User",
                                           "channel" => "Channel",
                                           "channel_head" => "Channel Information",
                                           "max" => $max,
                                           "channels" => $cAmount,
                                           "logintime" => "Login time",
                                           "idletime" => "Idle time",
                                           "channelstats" => $channelstats,
                                           "userstats" => $userstats));
					   
    echo $outp_str;				   
					   
    }
  } else {
    echo "Error, function fopen not found";
  }

?>