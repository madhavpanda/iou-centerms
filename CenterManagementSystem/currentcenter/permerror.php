<?php

include( "cfg.php" );
   include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   @include( "../lang/en_utf8/countries.php" );
$mysql_connect_id = mysql_start( $mysql_server, $moodle_db, $mysql_username, $mysql_password );
$sqlCountry = mysql_query("select distinct(`country`) as country from `mdl_user` where country<>'' and country is not null order by country");


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="http://bais.islamiconlineuniversity.com/bais/theme/standard/styles.php" />
<link rel="stylesheet" type="text/css" href="http://bais.islamiconlineuniversity.com/bais/theme/ingenuous/styles.php" />
<meta name="description" content="" />
<!--[if IE 7]>
		    <link rel="stylesheet" type="text/css" href="http://bais.islamiconlineuniversity.com/bais/theme/standard/styles_ie7.css" />
		<![endif]-->
<!--[if IE 6]>
		    <link rel="stylesheet" type="text/css" href="http://bais.islamiconlineuniversity.com/bais/theme/standard/styles_ie6.css" />
		<![endif]-->
<meta name="keywords" content="moodle, Islamic Online University - BAIS " />
<title>Islamic Online University - BAIS</title>
<link rel="shortcut icon" href="http://bais.islamiconlineuniversity.com/bais/theme/ingenuous/favicon.ico" />
<!--<style type="text/css">/*<![CDATA[*/ body{behavior:url(http://bais.islamiconlineuniversity.com/bais/lib/csshover.htc);} /*]]>*/</style>-->
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
<link href="css/date-css.css" rel="stylesheet" type="text/css">
<script type='text/javascript' src='js/zapatec.js'></script>
<script type="text/javascript" src="js/calendar.js"></script>
<script type="text/javascript" src="js/calendar-en.js"></script>
</head>
<body class="course course-1 notloggedin dir-ltr lang-en_utf8" id="site-index" width="100%">
<div id="page" width="100%">
<div id="header-home" class=" clearfix" width="100%">
  <div class="wrapper clearfix"> <img class="logo" src="http://bais.islamiconlineuniversity.com/bais/theme/ingenuous/pix/logobanner.png"/>
    <div class="headermenu">
      <div class="logininfo"></div>
    </div>
  </div>
  <div id="menubox" class="wrapper clearfix">
    <ul id="menu">
      <li><a href="http://bais.islamiconlineuniversity.com/bais" title="Home">Home</a></li>
      <li><a href="http://bais.islamiconlineuniversity.com/bais/course/index.php" title="Courses">Courses</a></li>
      <li><a href="http://bais.islamiconlineuniversity.com/bais/calendar/view.php?view=month" title="Calendar">Calendar</a></li>
    </ul>
  </div>
</div>
<div class="background" width="100%">
<p>&nbsp;</p>

<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">


<tr> 
<td class="headerblue"><h2>Permission Denied</h2></td>
</tr>
<tr><td> You do not have the permission to view the requested page.This could either be due to the fact that you may not be currently logged in or you are not enrolled in any course in the University.Thank you.</td>
</tr>

<tr><td>
<div class="generalbox categorybox box">
              <a href="login/index.php">Click Here to login</a>
              </td></tr>

</table>