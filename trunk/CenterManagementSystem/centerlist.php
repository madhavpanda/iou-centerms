<?   

require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
   //require_once('/home/eomanico/public_html/ecampus/config.php');
  // require_once($CFG->dirroot .'/course/lib.php');
   //require_once($CFG->dirroot .'/lib/blocklib.php');
   


   //unset($_SESSION['sch_page_name']);
    include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password );
 if ( $USER->id ) { 
   
   ?>
<link rel="stylesheet" href="CenterCMS.css" />

<link rel="shortcut icon" href="http://bais.islamiconlineuniversity.com/bais/theme/ingenuous/favicon.ico" />
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
     <link rel="stylesheet" href="CenterCMS.css" />
    <link rel="stylesheet" href="bootstrap.css" />
    
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>
<script>
/*function showCenterList(value)
	{
	   $('#centerlist').fadeOut('2000', function(){});
	    $('#centerlist').empty().html('<img src="loading_animation.gif" />')
	   $('#centerlist').load("nucenterlist.php?cid="+value).fadeIn('200');
		}

		function showMap(val,value)
	{
	 // location.replace("centerlist2.php?ctrid="+val)
	
	
	
	}*/
</script>	

<link rel="stylesheet" href="CenterCMS.css" />
	<title>Islamic Online University: List of Exam Centers in your country </title>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>



<title>List of exam centers in your country</title>
<style type="text/css">

 #maplist { width: 480px; height: 300px; border: 0px; padding: 0px; }
 </style>
<script language="javascript" type="text/javascript">
            //document.documentElement.className = "js";
</script>
<script
	src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js">
</script>
<script src="js/jquery.cookie.js"></script>
<script src="js/jquery.collapse.js"></script>
<script type="text/javascript">
            $(function(){
                $(".question").collapse({
                    show: function()
                    {
                        this.animate({
                            opacity : 'toggle',
                            height : 'toggle'
                        }, 600);
                    }, 
                    hide : function()
                    {
                        this.animate({
                            opacity : 'toggle',
                            height : 'toggle'
                        }, 600);
                    }
                });

               
            });
   
 
</script>

</head>

<body onload='load()' style="margin: 0px; padding: 0px; font-family: 'Trebuchet MS',verdana;">

<table width="100%"  cellpadding="10" cellspacing="0" border="0" style="background-image:url('bground.pnp');height:100%">
<tr class="menu">

<!-- ============ HEADER SECTION ============== 
<td colspan="2" style="height: 100px;" bgcolor="#777d6a"><h1>Website Logo</h1></td>-->
<td colspan="2" align= "center" style="margin: 0px; padding: 0px; font-family: 'Trebuchet MS',verdana;"><strong>Lists of Exam Centers in your Country</strong></td>
</tr>

<tr>
<!-- ============ LEFT COLUMN (MENU) ============== -->
<td  valign="top"  style="width:585px;background-image:url('bground.png')">
<div style="background-image:url('bground.png')">
<h3><?
echo "Please Choose your country ".'<br />';
   //echo $mysql_connect_id;
   
echo '<form id="form1" method="POST" action="centerlist.php">';
 $query="SELECT CountryId,CountryCode,CountryName FROM Countries";

/* You can add order by clause to the sql statement if the names are to be displayed in alphabetical order */

$result = mysql_query ($query);
echo "<select name=country value='' >Country Name</option>";

echo "<option value=''>Choose a country</option>";
//onClick='showCenterList(this.value);'
// printing the list box select command

while($nt=mysql_fetch_array($result)){//Array or records stored in $nt
echo "<option value=$nt[CountryId]>$nt[CountryName]</option>";
/* Option values are added by looping through the array */
}

  echo '<input type="submit" name="submit" id="button" value="Select" />';
echo "</select>";// Closing of list box   

echo '</form>';
 ?></h3>
 
 <br />
<div id="centerlist" style="width:575px; table-layout:fixed;padding-top:-20px" border="0">
<table style="width:575px;  border:1px solid #CCC;border-collapse      : collapse;font: small/1.5 "Tahoma", "Bitstream Vera Sans", Verdana, Helvetica, sans-serif;">
 <tr align="center" style="background            : #fafafb;
  border-top            : 1px solid #CCC;
  text-align            : left;
  font-weight           : normal;">
    
   
    <td >City</td>
    <td>Center Name</td>
     <td >Available</td>
    <td >Register</td>
  
  </tr>	
<?php

   
     if (isset($_POST['country'])) {

 $country_id=$_POST['country'];
	 }
	 else { 
	 $country_id=174; 
	 }
 
//echo $country_id;
$getcenter= "Select * from ExamCenters where CountryId=$country_id and Active =1 order by CityName";
//echo $getcenter;
$getres = mysql_query($getcenter);
$checkempty=mysql_num_rows($getres);
if (!empty($checkempty)){

while($record=mysql_fetch_array($getres)){
	$countryid=$record['CountryId'];
	//echo $countryid;
	$countryqry= "Select * from Countries where CountryId=$countryid";
	//echo $countryqry;
	$getcountry=mysql_query($countryqry);
	$countryrec=mysql_fetch_array($getcountry);
		$country=$countryrec['CountryName'];
	
	$city=$record['CityName'];
 $active=$record['Active'];
 $centername=stripslashes($record['CenterName']);
  $address=$record['Address1'];
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
$activated = $record['Activated'];
?>

 <tr  class="menu" style="background-color:#E4E4E4;color:#000">
     
  
  
    <td> <?php print $city ?> </td> 
    <td><div class="question" style="text-decoration:none
    ;color: #36F; font-family:Georgia, 'Times New Roman', Times, serif;"><span > <?php print $centername ?> </span>
    <div class="answer">
       <?php print "Address: ".$address.'<br />'.'<br />'."Email: ".$centeremail.'<br />'.'<br />'."Phone: ".$centerphone ?> 
    </div>
    
    </div></td>
    <td> <?php echo $activated == 1? 'Yes' : 'No'; ?></td>
     <td style=" padding-top:15px;" align="center"> <form id="reg" method="POST" action="centrereq.php?cname=<?php echo stripslashes( $centername); ?>&&ctry=<?php echo $country; ?>&&cty=<?php echo $city; ?>">
	<!--<input type="submit" name="enroll" value="Choose" />-->
     <?php 
	 $statussql=mysql_query("select status from regstatus where id =1");
$statusrow=mysql_fetch_object($statussql);				
if ($statusrow->status=='inactive')
{
	echo "<strong>Registration <br />Suspended</strong>";
}
else{	 
     if ($activated == 1)
     {
		 //echo "Yes";
     	echo '<input  type="submit" name="enroll" value="Choose" />';
     } 
     else
     {
		 //echo "No";
     echo '<input  type="submit" name="enroll" value="Choose" disabled="disabled" />';
     }
}
     ?>
    </form></td>
   
    </tr>






 
<?

}   
}
elseif(empty($checkempty)) {
	 echo '<tr  bgcolor="#C9C9C9">'; 
	echo '<td colspan="4">';
	echo 'No centers available in your country';
	echo '</td>';
	  echo '</tr>';
  }
//}
?>
</table></div>

<div style=" clear:both"></div>
<div style="width: auto; table-layout:fixed">
<br><br>
  <p>Please click on the Center name to get its address.</p>
  <p>If You have found no available centers or no suitable centers please suggest a likely center you would like to take your exams <a href="centersuggest.php" style="color: maroon; font-weight:bold;"> here</a></p>
</div>
<div style=" clear:both"></div>
<div style="width: auto; table-layout:fixed; padding:10px; margin-top:8px; ">
<?
$errorid=$_REQUEST[err];
if($errorid==1){
	echo ' <p style="color:#F00;font-size:18px;font-weight:bold;font-family:Times New Roman, Times, serif">Dear Students the time for registering  for the exam center has passed.Therefore your registration was not accepted.Thank You. 	</p>';
	}
	elseif($errorid==2){
		

	echo ' <p style="color:#F00;font-size:18px;font-weight:bold;font-family:Times New Roman, Times, serif ">Dear Student,<br /> Your exams center registration data already exist.You may not register again.Thank You.	</p>';
	}
?>
</div>
</div>
</td>

<!-- ============ RIGHT COLUMN (CONTENT) ============== -->
<td width="55%" valign="top" >

<h2 style=" color:#222; "> IOU Exam Centers in <? 
if($country){
$countryname=$country;
}
else{
$name=	mysql_extract_records_where("","Countries","CountryId=$country_id","CountryName");
$countryname=$name[1][CountryName];
}

echo $countryname;?> </h2>
<?
//echo $_POST['ctrid'];

if (isset ($_POST['country'])) {	
//if ($_POST['submit']=="Choose"){
require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
include_once("GoogleMap.php");
include_once("JSMin.php");   
$MAP_OBJECT = new GoogleMapAPI(); 
$MAP_OBJECT->_minify_js = isset($_REQUEST["min"])?FALSE:TRUE;
   //require_once('/home/eomanico/public_html/ecampus/config.php');
 //  require_once($CFG->dirroot .'/course/lib.php');
include( "centerconn.php" );
//require_once("simpleGMapAPI.php");
require_once("simpleGMapGeocoder.php");
$geo = new simpleGMapGeocoder();


$dbcnx= mysql_connect($mysql_server,$mysql_username,$mysql_password);
 mysql_select_db($center_db,$dbcnx) or die('no selection');


$country_id=$_POST['country'];
$getcenter= "Select * from ExamCenters where CountryId =$country_id and Active =1 order by CityName";
//echo $getcenter;
$getres = mysql_query($getcenter);
$mrk_cnt = 0;
while($record=mysql_fetch_array($getres)){
	$countryid=$record['CountryId'];
	//echo $countryid;
	$countryqry= "Select * from Countries where CountryId=$countryid";
	//echo $countryqry;
	$getcountry=mysql_query($countryqry);
	$countryrec=mysql_fetch_array($getcountry);
		$country=$countryrec['CountryName'];
	//$country="Bahrain'";
	$city=$record['CityName'];
 $active=$record['Active'];
 $centername=stripslashes($record['CenterName']);
  $address=stripslashes($record['Address1']);
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
$activated = $record['Activated'];
//  include the googlemap api files 

//echo $address.'<br />';
$MAP_OBJECT->addMarkerByAddress("$city, $country"," ", "$address");
/*
  $adds="$city , $country";
//echo $adds;
$x= $geo->getGeoCoords($adds);
//print_r($x);
//echo "<br />";
//$lat=$x[lat];
//echo "<br />";
//$lon=$x[lng];

          
*/

}	
	
	
?>
<?=$MAP_OBJECT->getHeaderJS();?>
<?=$MAP_OBJECT->getMapJS();?>

<?=$MAP_OBJECT->printOnLoad();?>
<?=$MAP_OBJECT->printMap();?>
<?=$MAP_OBJECT->printSidebar();?>




<?
}
else {
	//display qatar's map by default
	
	include_once("GoogleMap.php");
include_once("JSMin.php");   
$MAP_OBJECT = new GoogleMapAPI(); 
$MAP_OBJECT->_minify_js = isset($_REQUEST["min"])?FALSE:TRUE;

$MAP_OBJECT->addMarkerByAddress("Bin omran Doha, Qatar","IOU head office", "IOU head Office,bin Omran Doha");
?>
<?=$MAP_OBJECT->getHeaderJS();?>
<?=$MAP_OBJECT->getMapJS();?>

<?=$MAP_OBJECT->printOnLoad();?>
<?=$MAP_OBJECT->printMap();?>
<?=$MAP_OBJECT->printSidebar();?>
	
<?
}
?>

</td></tr>

<!-- ============ FOOTER SECTION ============== -->
<!--<tr><td colspan="2" align="center" height="20" bgcolor="#777d6a">Copyright ©</td></tr>-->
<tr  width= "100%" class="menu">
	<td colspan="2" align= "center">
    <span><strong>Islamic Online University Examination Centers Portal</strong></span><br />
    <marquee direction="left" scrolldelay="45" scrollamount="1" truespeed="truespeed" onmouseover="this.stop();" onmouseout="this.start();"> 
    <span><strong>If You have found no available centers or no suitable centers please suggest a likely center you would like to take your exams <big> <a href="centersuggest.php" style="text-decoration:none; color:#CCC;"> here</a></big></strong></span>   </marquee></td
></table>

<?
  }






 else {
 header("location: loginerror.php");
 //exit;
             
   }
   ?>
</body>


</html>