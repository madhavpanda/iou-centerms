<? #session_start();
  
   require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
   //require_once('/home/eomanico/public_html/ecampus/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   


   //unset($_SESSION['sch_page_name']);
    include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password );
   
   ?>
<link rel="stylesheet" href="CenterCMS.css" />

<link rel="shortcut icon" href="http://bais.islamiconlineuniversity.com/bais/theme/ingenuous/favicon.ico" />
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
.td_data td  {
	color:#FFFFFF;
	text-decoration:none;
	cursor:pointer;
}
a:link {
        color:#FFF;
	text-decoration: none;
}
a:visited {
        color:#FFF;
	text-decoration: none;
}
a:hover {
        color:#FFF;
	text-decoration: underline;
}
a:active {
        color:#FFF;
	text-decoration: none;
}
-->
</style>
<title>Islamic Online University Exam Centers Registration</title>
<table width= "100%" border="0">
<tr  width= "100%"  class="menu">
	<td width= "87%" align= "center">EXAM CENTERS REGISTRATION PORTAL <td width="13%"><a href="examcenternotice.php"><span><strong>Notice Board</strong></span></a></td></td>
      
</tr>
</table>




      <table id="layout-table" summary="layout" width="100%">

<tr>
          <td id="middle-column" width="100%" valign="top"><span id="maincontent"></span>
            
    "Sorry the operation was unsuccesful.This maybe that you are trying to add a center that already exists.Please contact the Center Manager.Thank you </td>
        </tr>
       
        </table>
<p><strong><a href="examreg.php">Exam Centers Registration Homepage</a></strong></p>
<table width= "100%" border="0">
<tr  width= "100%" class="menu">
	<td width= "100%" align= "center"><span><strong>Islamic Online University Exam Centers Registration Portal</strong></span></td></tr>
 </table>