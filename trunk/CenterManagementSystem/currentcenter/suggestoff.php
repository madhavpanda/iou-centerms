<?php
 

    require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
    //require_once('/home/eomanico/public_html/ecampus/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   
include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password ); 
   if ( $USER->id ) { 
 
 
?>
<link rel="stylesheet" href="CenterCMS.css" />
 <link rel="shortcut icon" href="http://bais.islamiconlineuniversity.com/bais/theme/ingenuous/favicon.ico" />
<style type="text/css">
<!--
fieldset
{
border: 1px solid #003372;
width: 20em
}
legend
{
color: #fff;
background: #003372;
border: 1px solid #FFFFFF;
padding: 2px 6px
} 
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
<title>Islamic Online University Exam Centers Registration</title>
<table width= "100%" border="0">
<tr  width= "100%" class="menu">
	<td width= "100%" align= "center"><span><strong>Exam Center Suggestion page</strong></span></td>
      
</tr>
</table>

<p>Dear students the time for submitting a NEW exam center for approval has passed ( it was 01/12 ) therefore the system will no longer accept submission of new centers.<br><br>
Thank You
.<br><br></p>
<p><strong><a href="examreg.php">Exam Centers Registration Homepage</a></strong></p>
<table width= "100%" border="0">
<tr  width= "100%" class="menu">
	<td width= "100%" align= "center"><span><strong>Islamic Online University Exam Centers Registration Portal</strong></span></td></tr>
 </table>
 <?
  }






 else {
 header("location: loginerror.php");
 //exit;
             
   }