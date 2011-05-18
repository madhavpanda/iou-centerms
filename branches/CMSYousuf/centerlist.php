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
	<td width= "100%" align= "center"><span><strong>Lists of Exam Centers in your Country</strong></span></td>
      
</tr>
</table>
<table>
<tr bgcolor="#000000" class="td_data">
     <td width="4%" bgcolor="#FFFFFF">&nbsp;</td>
     <td width="11%"><span><strong>Country</strong></span></td>
    <td  width="9%" style="color:#FFFFFF"><strong>City</strong></td>
    <td  width="12%"><span><strong>Center Name</strong></span></td>
    <td  width="12%"><span><strong>Center email	</strong></span></td>
    <td  width="12%"><span><strong>Phone no</strong></span></td>
    <td  width="20%"><span><strong>Web Adress</strong></span></td>
    <td  width="8%"><span><strong>Student Capacity</strong></span></td>
    <td  width="10%"><span><strong>Register/choose</strong></span></td>
    <td width="4%" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>	
<?php
if ( $USER->id ) {
	 //include( "cfg.php" );
  // include( "php_lib3/misc.php" );
  // include( "php_lib3/mysql.php" );
 //  @include( "../lang/en_utf8/countries.php" );
   
  if (isset ($_POST['country'])) {
  $country_id=$_POST['country'];
  //echo $country_id;
   
$getcenter= "Select * from ExamCenters where CountryId =$country_id and Active =1 ";
//echo $getcenter;
$getres = mysql_query($getcenter);
while($record=mysql_fetch_array($getres)){
	$countryid=$record['CountryId'];
	//echo $countryid;
	$countryqry= "Select * from Countries where CountryId=$countryid";
	//echo $countryqry;
	$getcountry=mysql_query($countryqry);
	while($countryrec=mysql_fetch_array($getcountry)){
		$country=$countryrec['CountryName'];
		//echo $country;
	}
	
 $cityid=$record['CityId'];
 //echo $cityid;
 $cityqry= "Select CityName from Cities where CityId=$cityid";
	//echo $cityqry;
	$getcity=mysql_query($cityqry);
	while($cityrec=mysql_fetch_array($getcity)){
		$city=$cityrec['CityName'];
		//echo $city;
	}
 $active=$record['Active'];
 $centername=$record['CenterName'];
 //echo $centername;
 $centeremail=$record['Email'];
// echo $centeremail;
 $centerphone=$record['PrimaryPhone'];
// echo $centerphone;
 $centerweb=$record['Website'];
 //echo $centerweb;
 $capacity=$record['Capacity'];
// echo $capacity
 //$register=$record['register']
//echo $bg_color; 
?>
 <tr class="td_data" >
     
    <td> <?php  ?></td>
    <td> <?php echo $country; //print $country ?> </td>
    <td> <?php print $city ?> </td> 
    <td> <?php print $centername ?> </td>
    <td> <?php print $centeremail ?> </td>
    <td> <?php print $centerphone ?> </td>
    <td> <?php print $centerweb ?> </td>
    <td> <?php print $capacity ?> </td>
     <td> <form id="reg" method="POST" action="centrereq.php?cname=<?php echo $centername; ?>&&ctry=<?php echo $country; ?>&&cty=<?php echo $city; ?>">
	<input type="submit" name="enroll" value="Choose" />
    </form></td>
    
    <td> &nbsp;</td>
    </tr>

<? }   

}
  }





 else {
 echo "No way u aint logged in yet";
	 //$arrFileInfo = explode("/",$_SERVER['SCRIPT_NAME']);
	//$_SESSION['sch_page_name'] = "/".$arrFileInfo[count($arrFileInfo)-1]

   }
  
 ?>
 </table> 
 <table width= "100%" border="0">
<tr  width= "100%" bgcolor="#000000" class="td_data">
	<td width= "100%" align= "center"><span><strong>If You have found no available centers or no suitable centers please suggest a likely center you would like to take your exams <a href="http://www.eomani.com/ecampus/center/centersuggest.php"> here</a></strong></span></td>
 </table>
  