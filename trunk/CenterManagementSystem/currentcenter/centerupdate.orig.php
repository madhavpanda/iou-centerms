<? #session_start();
include("accesscontrol.php");

  
  require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
  // require_once('/home/eomanico/public_html/ecampus/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   


   //unset($_SESSION['sch_page_name']);
    include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password );
   
   ?>
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
	<td width= "100%" align= "center"><span><strong>Update Exam Centers Information</strong></span></td>
      
</tr>
</table>
<? include_once("linkmenu.php");?>
<table>
<tr class="menu">
     
     <td width="11%"><span><strong>Country</strong></span></td>
    <td  width="9%" style="color:#FFFFFF"><strong>City</strong></td>
    <td  width="12%"><span><strong>Center Name</strong></span></td>
    <td  width="12%"><span><strong>Address</strong></span></td>
    <td  width="8%"><span><strong>Center email and Website	</strong></span></td>
    <td  width="12%"><span><strong>Phone no</strong></span></td>
    
    <!--<td  width="5%"><span><strong>Student Capacity</strong></span></td>-->
   
    <td  width="10%"><span><strong>Remove Center</strong></span></td>
     <td  width="8%"><span><strong>Status</strong></span></td>
     <td width="3%"><span><strong>Center Status</strong></span></td>
    <td width="5%" align="center"><span><strong> Update</strong></span></td>
  </tr>	
<?php
// if ( $USER->id ) {
	 //include( "cfg.php" );
  // include( "php_lib3/misc.php" );
  // include( "php_lib3/mysql.php" );
 //  @include( "../lang/en_utf8/countries.php" );
   
  //if (isset ($_POST['country'])) {
 // $country_id=$_POST['country'];
  //echo $country_id;
   
$allcenter= "Select * from ExamCenters order by CountryId ";
//echo $allcenter;
$getall = mysql_query($allcenter);
while($record=mysql_fetch_array($getall)){
	$countryid=$record['CountryId'];
	//echo $countryid;
	$countryqry= "Select * from Countries where CountryId=$countryid";
	//echo $countryqry;
	$getcountry=mysql_query($countryqry);
	while($countryrec=mysql_fetch_array($getcountry)){
		$country=$countryrec['CountryName'];
		//echo $country;
	}
	
 //$cityid=$record['CityId'];
 //echo $cityid;
 //$cityqry= "Select CityName from Cities where CityId=$cityid";
	//echo $cityqry;
	//$getcity=mysql_query($cityqry);
	//while($cityrec=mysql_fetch_array($getcity)){
		//$city=$cityrec['CityName'];
		//echo $city;
	//}
$city=$record['CityName'];	
 $active=$record['Active'];
 if ($active==0){
  // $status="<a href='statusrev.php?who=$record[EnteredById]' onclick='return popitup(statusrev.php?who=$record[EnteredById])'>Unapproved</a>";
   
  $status="<a href='statusrev.php?who=$record[EnteredById]&&centid=$record[CenterId]' target='_blank'> Unapproved </a>";
 }
 else{$status="Approved";}
 
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
 $enteredby=$record['EnteredById'];
  $address=$record['Address1'];
 $centerid=$record['CenterId']; 
 $centid2=$record['CenterId']; 
//echo $bg_color; 
$center_status = $record['Activated'];
// If Center Status is 1 then the center is activated
if ($center_status == 0)
{
	$center_status_link_text = "Activate";
}
else // Center is deactivated
{
	$center_status_link_text = "Deactivate";
}
$center_status_link = "<a href='centerstatus.php?cid=$centid2' target='_blank'>$center_status_link_text</a>";
?>
 <tr bgcolor="#C9C9C9" >
     
    
    <td> <?php echo $country; //print $country ?> </td>
    <td> <?php print $city ?> </td> 
    <td> <?php print $centername ?> </td>
    <td> <?php print $address ?> </td>
    <td> <?php print $centeremail .'<br />'.$centerweb ?> </td>
    <td> <?php print $centerphone ?> </td>
    
    <!--<td> <?php /* print $capacity */ ?> </td>-->
   
   
    <td> <a href="removecenter.php?cid=<?=$centid2 ?>" target="_blank"> Erase Center</a></td>
     <td> <?php print $status ?> </td>
      <td> <?php print $center_status_link ?> </td>
     <td> <form id="Update" method="POST" action="updatecenter.php?centerid=<?php echo $record['CenterId'];?>">
	<input type="submit" name="SendProc" value="Edit" />
    </form> </td>
    

    </tr>

<? }   

//}
  //}





 //else {
 //echo "No way u aint logged in yet";
	 //$arrFileInfo = explode("/",$_SERVER['SCRIPT_NAME']);
	//$_SESSION['sch_page_name'] = "/".$arrFileInfo[count($arrFileInfo)-1]

 //  }
  
 ?>
 </table> 
 <table width= "100%" border="0">
<tr  width= "100%" class="menu">
	<td width= "100%" align= "center"><span><strong>Islamic Online University Exam Centers Registration Portal</strong></span></td
 ></table>
  <script>
	
		
function popitup(url) {
	newwindow=window.open(url,'name','height = 380, width = 550,left=350,top=200');
	if (window.focus) {newwindow.focus()}
	return false;
}

</script>