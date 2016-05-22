<?php //  header("Content-type: text/html; charset=iso-8859-1");?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
            "http://www.w3.org/TR/html4/loose.dtd">
<html>
 <head>
  <meta http-equiv="Content-type" value="text/html; charset=iso-8859-1">
<title>AutoDiscover</title>
<link rel="Stylesheet" type="text/css" href="/thruk/themes/Thruk/stylesheets/all_in_one-2.02-1.css">
<?PHP
include("functions.php");
$function=$_GET["function"];
if($function=="removeFoundHost"){
      $ip=$_GET["ip"];
    showremoveFoundHost($ip);
    } else if($function=="IgnoreFoundHost"){
      $ip=$_GET["ip"];
    showIgnoreFoundHost($ip);
    } else if($function=="ActiveScan"){
    showActiveScan();
    } else if($function=="RemoveNetwork"){
      $ip=$_GET["ip"];
    showRemoveNetwork($ip);
    } else if($function=="AddNetwork"){
	$address=$_POST["address"];
    showAddNetwork($address);
    } else if($function=="ChangeCommunity"){
	$comm=$_POST["comm"];
    showChangeCommunity($comm);
        } else if($function=="MoreInfo"){
	$ip=$_GET["ip"];
    showMoreInfo($ip);
        } else if($function=="ChangeHostName"){
	$ip=$_GET["ip"];
	$hostname=$_GET["hostname"];
    showChangeHostName($ip,$hostname);
        } else if($function=="DoChangeHostName"){
	$hostname=$_POST["hostname"];
	$ip=$_GET["ip"];
    showDoChangeHostName($ip,$hostname);
        } else if($function=="AddHostToNagios"){
	$hostname=$_GET["hostname"];
	$ip=$_GET["ip"];
    showAddHostToNagios($ip,$hostname);
}
?>
</head>
<body>
<br>
<div id="layout">
De ingestelde communitystring is:
<?php showCommunity(); ?>

<br>
De te scannen netwerk(en):<br>
<?php showNetworks(); ?>
<form action="index.php?function=AddNetwork" method="POST">
<input type="text" name="address" value="">
<input type="image" src="images/add.png" width=20px alt="Submit Form" />
</form>
<a href=index.php?function=ActiveScan><img src=./images/search.png width=20px>Scan now!</a>
<br>
<br>
<table>
<tr><td><img src=./images/add.png width=20px></td><td>Add Host to NagiosQL (needs a little config at NagiosQL)</td></tr>
<tr><td><img src=./images/cancel.png width=20px></td><td>Delete Host from this list</td></tr>
<tr><td><img src=./images/info.jpg width=20px></td><td>Get more info of this Host</td></tr>
<tr><td><img src=./images/edit.png width=20px></td><td>Edit the Hostname</td></tr>
<tr><td><img src=./images/lock.png width=20px"></td><td>Move Host to Ignore list</td></tr>
</table>
<h2>New hosts:</h2>
<table>
<tr><td width=200>Address:</td><td width=200>Hostname </td><td colspan=3>Action</td</tr>
<?php showFoundHostsNI(); ?>
</table>
<h2>Ignored hosts:</h2>
<table>
<tr><td width=200>Address:</td><td width=200>Hostname </td><td colspan=3>Action</td</tr>
<?php showFoundHostsI(); ?>
</table>
</div>
</body>
</html>

