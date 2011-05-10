<? #session_start();
  
   require_once('/home/eomanico/public_html/ecampus/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   


   //unset($_SESSION['sch_page_name']);
    include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password );
   
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
.td_data td  {
	color:#FFFFFF;
	text-decoration:none;
	cursor:pointer;
}
-->
</style>
<table width= "100%" border="0">
<tr  width= "100%" bgcolor="#000000" class="td_data">
	<td width= "100%" align= "center">EXAM CENTERS REGISTRATION PORTAL</td>
      
</tr>
</table>




      <table id="layout-table" summary="layout" width="100%">

<tr>
          <td id="middle-column" width="100%" valign="top"><span id="maincontent"></span>
            
         <p><? echo "Welcome ".$USER->lastname.","?> </p>
    <p>All necessary Instructions go here plus the student is expected to choose his country from below to have a list of centers available in the country displayed</p>
        </td>
        </tr>
        </table>
<?
echo "Please Choose the country in which you wish to take your exams".'<br />';
   //echo $mysql_connect_id;
   
echo '<form id="form1" method="POST" action="centerlist.php">';
 $query="SELECT CountryId,CountryCode,CountryName FROM Countries";

/* You can add order by clause to the sql statement if the names are to be displayed in alphabetical order */

$result = mysql_query ($query);
echo "<select name=country value=''>Country Name</option>";
// printing the list box select command

while($nt=mysql_fetch_array($result)){//Array or records stored in $nt
echo "<option value=$nt[CountryId]>$nt[CountryName]</option>";
/* Option values are added by looping through the array */
}
  echo '<input type="submit" name="submit" id="button" value="Choose" />';
echo "</select>";// Closing of list box   

echo '</form>';
 
/*
if ( $USER->id ) {
	
  if (isset ($_POST['country'])) {
  $country_id=$_POST['country'];
  echo $country_id;
   }
   }*/