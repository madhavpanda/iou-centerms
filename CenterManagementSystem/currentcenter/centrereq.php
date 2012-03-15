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
<?
 #session_start();
  
  require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   


   //unset($_SESSION['sch_page_name']);
    include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password );
   
   
//$id=$_GET['userid'];
$center=$_GET['cname'];
$countryname=$_GET['ctry'];
$cityname=$_GET['cty'];
//echo $USER->firstname.''.$USER->lastname.''. $USER->email.'<br />';

//echo $x.' '.$y.' '.$z;
$firstname=mysql_real_escape_string($USER->firstname);
$lastname=mysql_real_escape_string($USER->lastname);
$email=mysql_real_escape_string($USER->email);
$center=mysql_real_escape_string($center);
$cityname=mysql_real_escape_string($cityname);
$countryname=mysql_real_escape_string($countryname);

	if($_POST['enroll'])
{
	$infoqry="Insert into CentreRequest ( FirstName, LastName, Email, Centername, City, Country ) values ('".$firstname."', '".$lastname."', '".$email."', '".$center."', '".$cityname."', '".$countryname."')";
	//echo $infoqry;
	$infores=mysql_query($infoqry);
	if($infores) {
echo	
'<table width= "100%" border="0">
<tr  width= "100%" class="menu">
	<td width= "100%" align= "center"><span><strong>Exam Center registration successfully completed!!!</strong></span></td>
      
</tr>
</table>';	
	
	
	}
	else{
	echo	
'<table width= "100%" border="0">
<tr  width= "100%" bgcolor="#000000" class="td_data">
	<td width= "100%" align= "center"><span><strong>Sorry!!! there was a problem with your Registration </strong></span></td>
      
</tr>
</table>';
	
}	
}
?>

<title>Islamic Online University Exam Centers Registration</title>

<a href="http://bais.islamiconlineuniversity.com/bais/">Home</a>	  