<?php
 
 include("accesscontrol.php");
require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
   // require_once('/home/eomanico/public_html/ecampus/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   
include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password ); 
 #session_start();
 
 //Fetch Values from Querystring
 
 $CenterId=$_GET['centerid'];
 $allcenter= "Select * from ExamCenters WHERE CenterId = '".$CenterId."' order by CountryId";

 $getall = mysql_query($allcenter);
while($record=mysql_fetch_array($getall)){
	$countryid=$record['CountryId'];
	$countryqry= "Select * from Countries where CountryId=$countryid";
	$getcountry=mysql_query($countryqry);
	while($countryrec=mysql_fetch_array($getcountry)){
		$country=$countryrec['CountryName'];
		}
	$city=$record['CityName'];
    $centername=$record['CenterName'];
    $centeremail=$record['Email'];
    $centerphone=$record['PrimaryPhone'];
    $centerweb=$record['Website'];
	$address=$record['Address1'];
	$state=$record['State'];

}
 
if (isset($_POST['Submit'])) { 
 
//function to ensure the value is numeric
function fnValidateNumber($value) {    
 if(ereg("\+?([0-9]{3})-?([0-9]{6,7})", $value) || $value == "") {
    return true;
} else {
    return false;
}  

 } 

//Function to sanitize values received from the form. Prevents SQL injection
	function clean($str) {
		$str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);
	}

//function to sanitize email
function EmailValidation($email) { 
   return true;
    //$email = htmlspecialchars(stripslashes(strip_tags($email))); //parse unnecessary characters to prevent exploits
//    
//    if ( eregi ( '[a-z||0-9]@[a-z||0-9].[a-z]', $email ) ) { //checks to make sure the email address is in a valid format
//    $domain = explode( "@", $email ); //get the domain name
//        
//        if ( @fsockopen ($domain[1],80,$errno,$errstr,3)) {
//            //if the connection can be established, the email address is probabley valid
//            return true;
//            /*
//            
//            GENERATE A VERIFICATION EMAIL
//            
//            */
//            
//        } else {
//            return false; //if a connection cannot be established return false
//        }
    
   // } else {
//        return false; //if email address is an invalid format return false
//    }
} 



//collect the values from the form

$centername = $_POST['CenterName'];
$email = $_POST['Email'];
$website=$_POST['Website'];
$address=$_POST['Address1'];
$phoneno=$_POST['PrimaryPhone'];
$city=$_POST['CityName'];
$state=$_POST['state'];
$countryid=$_POST['country'];

	
	//Sanitize the POST values
	 $centername = clean($_POST['CenterName']);
	 $email = clean($_POST['Email']);
	$website=clean($_POST['Website']);
	$address=clean ($_POST['Address1']);
	$phoneno=clean ($_POST['PrimaryPhone']);
	$countryid=clean ($_POST['country']);
	$city=clean ($_POST['CityName']);
	$state=clean ($_POST['state']);
	
	
	$errors = array();
	
	
	if (!EmailValidation($email))
	{
		$errors[]= "The email appears to be in valid";
	}
 
	if(strlen($centername)==0)

{

$errors[]="Please specify a Center Name";

}

if(strlen($address)==0)

{

 $errors[]="Please provide the Center's Address";

}
if(strlen($city)==0)

{

   $errors[]="The Center's City was not specified.";

}


if(strlen($state)==0)

{

   $errors[]="Ensure the state or county of the Center is added";

}


if(!fnValidateNumber($phoneno))

{

   $errors[]="Number is not valid, please try removing spaces and enter without the ISD code";

}
// were there any errors?  

if(count($errors) > 0)  

{  

    $errorString = '<p>There was an error processing the form.</p>';  

    $errorString .= '<ul>';  

    foreach($errors as $error)  

    {  

        $errorString .= "<li>$error</li>";  

    }  

    $errorString .= '</ul>';  

   echo $errorString;

    // display the previous form  

    //include 'centersuggest.php';  

}  

else 

{  
$oldquery = "Select * from ExamCenters where CenterID=$CenterId"; 


$old = mysql_fetch_array(mysql_query($oldquery)); 



$centername = empty($centername) ? $old['CenterName'] : $centername; 
$email = empty($email) ? $old['Email'] : $email; 
$website = empty($website) ? $old['Website'] : $website; 
$address = empty($address) ? $old['Address1'] : $address; 
$phoneno = empty($phoneno) ? $old['PrimaryPhone'] : $phoneno;
$city = empty($city) ? $old['CityName'] : $city; 
$state = empty($state) ? $old['State'] : $state; 
//$countryid = empty($countryid) ? $old['CountryId'] : $countryid; 
 



$query1 = "UPDATE ExamCenters SET Email = '".$email."',
 CenterName = '".$centername."',
  Website = '".$website."',
   Address1 = '".$address."',
	 CityName = '".$city."',
	  State = '".$state."',
	   PrimaryPhone = '".$phoneno."',
	     CountryId = '".$countryid."'
	    
	   
WHERE CenterId = '".$CenterId."'"; 


$result1=mysql_query($query1) or die("Oops!!! sorry please try again");

if(!mysql_error())
{
//header("location: submission_succesful.php");
echo "Operation Succesfull.Further instructions will be sent to your email address soon.Thank you";
}
}
}


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
<table width= "100%" border="0">
  <tr  width= "100%"  class="menu">
	<td width= "100%" align= "center" ><span><strong>Exam Center Suggestion page</strong></span></td>
      
</tr>
</table>
<? include_once("linkmenu.php");?>

<p>Update the Center details below </p>

<form id="form1" name="form1" method="post" action="">
  <fieldset>
   <legend>Center Information</legend>
   <table width="726" height="465" border="0">
    <tr>
      <td width="123">Center Name:*</td>
      <td width="587"><input class="text" type="text" name="CenterName" id="CenterName" value="<?php echo $centername; ?>" /></td>
    </tr>
    <tr>
      <td align="left">Country*</td>
      <td><?php
      
       
  $query="SELECT CountryId,CountryCode,CountryName FROM Countries";

$result = mysql_query ($query);
echo "<select name=country value=''>Country Name</option>";


while($nt=mysql_fetch_array($result)){ ?>
 <option value=<?php echo $nt[CountryId]?><?php if ($nt[CountryId]==$countryid){ ?> selected="selected"<?php } ?>><?php echo $nt[CountryName] ?></option>"; 
<?php }

 
echo "</select>"; 
?></td>

    </tr>
    <tr>
      <td align="left"><label for="stateID">State/County *</label></td>
      <td><input class="text" type="text" name="state" id="state" value="<?php echo $state; ?>"/></td>
    </tr>
    <tr>
      <td align="left"><label for="CityID2">City*</label></td>
      <td><input class="text"type="text" name="CityName" id="CityName" value="<?php echo $city; ?>"/></td>
    </tr>
    <tr>
      <td height="103" align="left" valign="top">Address*</td>
      <td>
        <textarea name="Address1" id="Address1" cols="45" rows="5" > <?php echo $address; ?></textarea>
     
      </td>
    </tr>
    <tr>
      <td height="20" align="left" valign="top">Contact Information</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td height="30" align="left" valign="top">Center Phone*
      </td>
      <td valign="top">
        <input  class="text" type="text" name="PrimaryPhone" id="PrimaryPhone"  value="<?php echo $centerphone; ?>" />
      
      
     </td>
    </tr>
    <tr>
      <td height="34" align="left" valign="top"><label for="Email3" >Center Email*</label></td>
      <td valign="top"><input class="text" type="text" name="Email" id="Email" value="<?php echo $centeremail; ?>"/></td>
    </tr>
    <tr>
      <td height="32" align="left" valign="top">Center Website</td>
      <td valign="top"><input type="text" class="text" name="Website" id="Website2" value="<?php echo $centerweb; ?>" /></td>
    </tr>
    <tr>
      <td height="96" align="left" valign="center"><input  type="submit" name="Submit" id="Submit" value="Update" /></td>
      <td>&nbsp;</td>
    </tr>
  </table>  
   </fieldset>
</form>
<p><a href="examreg.php">Exam Centers Registration Homepage</a></p>
<br/>
<table width= "100%" border="0">
<tr  width= "100%" bgcolor="#000000" class="td_data">
	<td width= "100%" align= "center" class="menu"><span><strong>Islamic Online University Exam Centers Registration Portal</strong></span></td></tr>
 </table>