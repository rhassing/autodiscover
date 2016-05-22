<?PHP
include("config.php");
global $host,$username,$password;
$db = mysql_connect("$host","$username","$password");
mysql_select_db("$database");

if(!$db){
	die("Kan geen database verbinding maken... sorry!");
}


function showNetworks(){
	$result = mysql_query("SELECT value FROM config WHERE what='address'");
        $count = 0;
       while ($row = mysql_fetch_array($result)){
		$value = $row["value"];
		echo "Network: $value <a href=\"index.php?function=RemoveNetwork&ip=$value\"><img src=./images/cancel.png width=20px></a><br>";
                $count++;
        }
        if ($count==0){
            print "geen netwerken";

        }
}

function showCommunity(){
	$result = mysql_query("SELECT value FROM config WHERE what='community'");
        $count = 0;
       while ($row = mysql_fetch_array($result)){
		$value = $row["value"];
		echo "<form action=\"index.php?function=ChangeCommunity\" method=\"POST\">";
		echo "<input type=\"password\" name=\"comm\" value=\"$value\">";
		echo "<input type=\"image\" src=\"images/edit.png\" width=20px alt=\"Submit Form\" />";
		echo "</form>";
                $count++;
        }
		 // echo "Geen community";
}

function showFoundHostsNI(){
	$result = mysql_query("SELECT ip,hostname,ignored FROM FoundHosts WHERE ignored = '0' ORDER BY ip ASC");
        $count = 0;
       while ($row = mysql_fetch_array($result)){
		$ip = $row["ip"];
		$hostname = $row["hostname"];
		$ignored = $row["ignored"];
		if ($ignored==0){
		echo "<tr><td>$ip</td>";
		echo "<td>$hostname</td>";
		echo "<td><a href=\"index.php?function=AddHostToNagios&ip=$ip&hostname=$hostname\"><img src=./images/add.png width=20px>";
		echo "<a href=\"index.php?function=removeFoundHost&ip=$ip\"><img src=./images/cancel.png width=20px></a>";
		echo "<a href=\"index.php?function=MoreInfo&ip=$ip\"><img src=./images/info.jpg width=20px alt=\"info\"></a>";
		echo "<a href=\"index.php?function=ChangeHostName&ip=$ip&hostname=$hostname\"><img src=./images/edit.png width=20px></a>";
		echo "<a href=\"index.php?function=IgnoreFoundHost&ip=$ip\"><img src=./images/lock.png width=20px alt=\"Ignore Host\"></a></td></tr>";
		}else{
		// echo "<tr><td>$ip</td><td>$hostname</td><td><input type=\"checkbox\" name=\"host\" value=\"ignored\" checked></td></tr>";
		}
                $count++;
        }
        if ($count==0){
		//echo "<tr><td>$ip</td><td>$hostname</td><td><input type=\"checkbox\" name=\"host\" value=\"ignored\" checked></td></tr>";
        }
}

function showFoundHostsI(){
	$result = mysql_query("SELECT ip,hostname,ignored FROM FoundHosts WHERE ignored = '1' ORDER BY ip ASC");
        $count = 0;
       while ($row = mysql_fetch_array($result)){
		$ip = $row["ip"];
		$hostname = $row["hostname"];
		$ignored = $row["ignored"];
		if ($ignored==0){
		echo "Hier klopt iets niet!";
		}else{
		echo "<tr><td>$ip</td>";
		echo "<td>$hostname</td>";
		echo "<td><a href=\"index.php?function=removeFoundHost&ip=$ip\"><img src=./images/cancel.png width=20px></a></td></tr>";
		}
                $count++;
        }
        if ($count==0){
                print "</table> <h2>There are no ignored hosts!";
        }
}

function showRemoveFoundHost($ip){
        mysql_query("DELETE FROM FoundHosts WHERE ip='$ip'");
		echo "Host permanent deleted";
}

function showIgnoreFoundHost($ip){
	mysql_query("UPDATE FoundHosts SET ignored='1' WHERE ip='$ip'");
		echo "Host ignored";
}

function showActiveScan(){
		echo "Active Scan";
		exec('/usr/local/bin/autodiscover.pl');
}


function showRemoveNetwork($ip){
        mysql_query("DELETE FROM config WHERE value='$ip'");
		echo "Network permanent deleted";
}

function showAddNetwork($address){
	if ($address==''){
	echo "Address cannot be empty!";
	} else {
	mysql_query("INSERT INTO  `autodiscover`.`config` ( `id` , `what` , `value`) VALUES ( '', 'address', '$address')");
		echo "Network $address added";
	}
}

function showChangeCommunity($comm){
	mysql_query("UPDATE config SET value='$comm' WHERE what='community'");
		echo "Community changed to $comm";
}

function showMoreInfo($ip){
		echo "Show more info of $ip";
		$output = `/usr/bin/nmap -sT $ip -Pn`;
		echo "<pre>$output</pre>";
}

function showChangeHostName($ip,$hostname){
		echo "Change the hostname ($hostname) for $ip";
		echo "<form action=\"index.php?function=DoChangeHostName&ip=$ip\" method=\"POST\">";
		echo "<input type=\"text\" name=\"hostname\" value=\"$hostname\">";
		echo "<input type=\"image\" src=\"images/edit.png\" width=20px alt=\"Submit Form\" />";
		echo "</form>";
}

function showDoChangeHostName($ip,$hostname){
		echo "Hostname for $ip changed to $hostname";
	mysql_query("UPDATE FoundHosts SET hostname='$hostname' WHERE ip='$ip'");
}
function showAddHostToNagios($ip,$hostname){
		echo "Host $hostname with ip: $ip added to Nagios, go to <a target=_blank href=\"/nagiosQL/\">NagiosQL</a> to finish the job";
        mysql_query("INSERT INTO `db_nagiosql_v2`.`tbl_host` (`id`, `host_name`, `alias`, `display_name`, `address`, `parents`, `parents_tploptions`, `hostgroups`, `hostgroups_tploptions`, `check_command`, `use_template`, `use_template_tploptions`, `initial_state`, `max_check_attempts`, `check_interval`, `retry_interval`, `active_checks_enabled`, `passive_checks_enabled`, `check_period`, `obsess_over_host`, `check_freshness`, `freshness_threshold`, `event_handler`, `event_handler_enabled`, `low_flap_threshold`, `high_flap_threshold`, `flap_detection_enabled`, `flap_detection_options`, `process_perf_data`, `retain_status_information`, `retain_nonstatus_information`, `contacts`, `contacts_tploptions`, `contact_groups`, `contact_groups_tploptions`, `notification_interval`, `notification_period`, `first_notification_delay`, `notification_options`, `notifications_enabled`, `stalking_options`, `notes`, `notes_url`, `action_url`, `icon_image`, `icon_image_alt`, `vrml_image`, `statusmap_image`, `2d_coords`, `3d_coords`, `use_variables`, `name`, `register`, `active`, `last_modified`, `access_group`, `config_id`) VALUES ('', '$hostname', '$hostname', '$hostname', '$ip', 0, 2, 0, 2, '3', 1, 2, 'o', 1, 5, 3, 1, 0, 1, 2, 1, NULL, 0, 2, NULL, NULL, 1, '', 1, 1, 2, 0, 2, 0, 2, 60, 1, NULL, 'd,r', 1, '', '', '', '', '', '', '', '', '', '', 0, '', '1', '1', '', 0, 1)");
        mysql_query("DELETE FROM `autodiscover`.`FoundHosts` WHERE ip='$ip'");
}






?>
