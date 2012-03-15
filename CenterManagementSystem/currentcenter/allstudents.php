<? #session_start();

  session_start();
if (!session_is_registered('id') || !session_is_registered('adminname'))
{
	header("Location: /admincms/login.php");
}

$acces_type = explode(", ",$_SESSION['access_type']);

if($_SESSION['access_type'] == "all")
{
	// Do nothing
	
}
else
{
	if(!in_array("Center Info", $acces_type))
	{
		header("Location: /admincms/index.php");
	}
}

   require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   


   //unset($_SESSION['sch_page_name']);
    //include( "centerconn.php" );
    include( "cfg.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   //$mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password );
   $mysql_connect_id = mysql_start( $mysql_server, $moodle_db, $mysql_username, $mysql_password );
   ?>

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
<tr  width= "100%" bgcolor="#000000" class="td_data">
	<td width= "100%" align= "center"><span><strong>All Students Status view</strong></span></td>
      
</tr>
</table>
<? include_once("linkmenu.php");?>

<?
echo "Am not sure we'll be needing this since we have similar info on the 'Centers Request' page or what do you think?";
?>
<table width= "100%" border="0">
<tr  width= "100%" bgcolor="#000000" class="td_data">
	<td width= "100%" align= "center"><span><strong>Islamic Online University Exam Centers Registration Portal</strong></span></td
 ></table>