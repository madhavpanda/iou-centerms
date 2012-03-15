<?php
include("accesscontrol.php");
#session_start();
  require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
   //require_once('/home/eomanico/public_html/ecampus/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   


    include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password );
$procid=$_GET['pid'];
$procid2=$_GET['pid'];

$query="select * from Proctors where ProctorId=$procid ";
//echo $query;
$delcent = mysql_fetch_array(mysql_query($query)); 



$procname=$delcent['ProctorName'];
//echo $centname;



?>

<link rel="shortcut icon" href="http://bais.islamiconlineuniversity.com/bais/theme/ingenuous/favicon.ico" />
<link rel="stylesheet" href="CenterCMS.css" />

<style type="text/css">
<!--
body {
	font-family: Arial;
	font-size: 10pt;
	line-height: normal;
}
table {
	font-family: Arial;
	font-size: 10pt;
	line-height: normal;
}
.verdana_10px {
	font-family: Verdana;
	font-size: 11px;
	line-height: normal;
}
.td_data td {
	padding: 4px
}
.td_data td span {
	color:#FFFFFF;
	text-decoration:none;
	cursor:pointer;}
.arial_10pt {
	font-family: Arial;
	font-size: 10pt;
	line-height: normal;
}
.verdana_10px {
	font-family: Verdana;
	font-size: 10px;
}
.tahoma_10pt {
	font-family: Tahoma;
	font-size: 10pt;
}
-->
</style>
<table width= "100%" border="0">
  <tr  width= "100%"  class="menu">
	<td width= "100%" align= "center" ><span><strong>Remove Proctor</strong></span></td>
      
</tr>
</table>


<? include_once("linkmenu.php");?>

<p>You are About to Delete a Proctor with the following details<br><br>

Proctor Name :<? print $procname; ?><br>
Proctor Post:<? print $delcent['ProctorPost']; ?><br>
Proctor Email  :<? print $delcent['ProctorMainEmail']; ?><br>
Proctor phone :<? print $delcent['PrimaryPhone']; ?><br>
 </p>

<table width= "100%" border="0">
  <tr  width= "100%"  class="menu">
	
	<td> You sure you want to delete this center</td>
	
	<td>
	<form method="POST" action="deleteproc.php?procid=<? echo $procid2;?> ">

<input type="submit" name="Submit" value="Yes">
</form>
</td>

<td>
<form method="POST" action="procupdate.php">

<input type="submit" name="submit" value="NO">
</form>
</td>
      
</tr>
</table>


 
 <table width= "100%" border="0">
<tr  width= "100%" class="menu">
	<td width= "100%" align= "center"><span><strong>Islamic Online University Exam Centers Registration Portal</strong></span></td></tr>
 </table>