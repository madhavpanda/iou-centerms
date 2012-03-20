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

<table width="100%" style="height: 100%;" cellpadding="10" cellspacing="0" border="0">
<tr class="menu">

<!-- ============ HEADER SECTION ============== 
<td colspan="2" style="height: 100px;" bgcolor="#777d6a"><h1>Website Logo</h1></td>-->
<td colspan="2" align= "center" style="margin: 0px; padding: 0px; font-family: 'Trebuchet MS',verdana;"><strong>Lists of Exam Centers in your Country</strong></td>
</tr>

<tr>
<!-- ============ LEFT COLUMN (MENU) ============== -->
<td width="45%" valign="top" bgcolor="#999f8e">

<h3><?
echo "Please Choose your country ".'<br />';
   //echo $mysql_connect_id;
   
echo '<form id="form1" method="POST" action="centerlist2.php">';
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
<div id="centerlist" style="width:45%; table-layout:fixed;" border="2">
<table width="45%"  border="0">
 <tr width="45%">
    
   
    <td>City</td>
    <td >Center Name</td>
     <td>Available</td>
    <td>Register</td>
  
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

 <tr bgcolor="#C9C9C9">
     
  
  
    <td> <?php print $city ?> </td> 
    <td><div class="question"><span> <?php print $centername ?> </span>
    <div class="answer">
       <?php print "Address: ".$address.'<br />'.'<br />'."Email: ".$centeremail.'<br />'.'<br />'."Phone: ".$centerphone ?> 
    </div>
    
    </div></td>
    <td> <?php echo $activated == 1? 'Yes' : 'No'; ?></td>
     <td> <form id="reg" method="POST" action="centrereq.php?cname=<?php echo stripslashes( $centername); ?>&&ctry=<?php echo $country; ?>&&cty=<?php echo $city; ?>">
	<!--<input type="submit" name="enroll" value="Choose" />-->
     <?php 
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
     ?>
    </form></td>
   
    </tr>






 
<?

}   
  
//}
?>
</table></div>
<br />
<div style="width: auto; table-layout:fixed">
If You have found no available centers or no suitable centers please suggest a likely center you would like to take your exams <a href="centersuggest.php" style="color: #fff;"> here</a></div>

</td>

<!-- ============ RIGHT COLUMN (CONTENT) ============== -->
<td width="55%" valign="top" >

<h2> IOU Exam Centers in <? echo $country ;?> </h2>
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
	<td  colspan="2" align= "center"><span><strong>Islamic Online University Exam Centers Registration Portal</strong></span></td></tr>
</table>

<?
  }






 else {
 header("location: loginerror.php");
 //exit;
             
   }
   ?>
</body>


</html>