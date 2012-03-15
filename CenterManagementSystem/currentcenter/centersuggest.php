<?php
 

    require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
  //  require_once('/home/eomanico/public_html/ecampus/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   
include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password );
   
   //check if the page is to be shown or not
   $checkpage = mysql_extract_records_where( "", "CenterSuggestPage", "id=1 ", "status" );
//echo $check;
$status=$checkpage[1]['status']; 

if ($status==1)// original suggest page offline redirect to suggest page expired
{
 header("location: suggestoff.php");
}
   if ( $USER->id ) { 
 #session_start();
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
    $email = htmlspecialchars(stripslashes(strip_tags($email))); //parse unnecessary characters to prevent exploits
    
    if ( eregi ( '[a-z||0-9]@[a-z||0-9].[a-z]', $email ) ) { //checks to make sure the email address is in a valid format
    return true;
	//$domain = explode( "@", $email ); //get the domain name
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
    
    } else {
        return false; //if email address is an invalid format return false
    }
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
if(strlen($countryid)==0)

{

  $errors[]="Ensure you choose a country please";

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


$query1= "insert into ExamCenters 
(CenterName, CityName, Address1, State, Website, DateTimeEntered, EnteredById, PrimaryPhone, Email, CountryId)

values ('" . $centername . "', '" . $city . "','" . $address . "','" . $state . "','" . $website . "', CURDATE(), '" . $USER->id . "','" . $phoneno . "','" . $email . "','" . $countryid . "')";

// Notice the single quotes that enclose

// email and name in the above



//$result1=mysql_query($query1) or die("Sorry the operation was unsuccesful.This maybe that you are trying to add a center that already exists.Please contact the Center Manager.Thank you ");

$result1=mysql_query($query1) or header("location: submiserror.php");
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
<title>Islamic Online University Exam Centers Registration</title>
<table width= "100%" border="0">
<tr  width= "100%" class="menu">
	<td width= "100%" align= "center"><span><strong>Exam Center Suggestion page</strong></span></td>
      
</tr>
</table>

Those who did not find a suitable center in our list may suggest a new center here. Before you suggest a center kindly read through our center requirements 
carefully to ensure that you suggest an appropriate center.<br /><br />
Before you 'suggest a center' for   approval you MUST HAVE already contacted the center and discussed with   them the requirements of acting as an exam center and then ONLY IF THEY   AGREE send their details to us for approval.<br>
  <br>
  
  Please fill in all the details completely as this information is vital for the process of center approval.<br><br>
  
  Once your center is approved you will be informed and requested to 'choose' the center in our centers management system.<br><br>
  It takes quite some time to approve and confirm a new center and therefore requests for new centers will not be accepted after 04/06/2012, and you may then be <br>
  <br>
  required to travel to the nearest IOU approved center. We urge you therefore to submit your requests as soon as possible and certainly by the 04/06/2012.<br><br>
</p>

<form id="form1" name="form1" method="post" action="">
<fieldset>
 <legend>Center Suggest Form</legend>
  <table width="80%" height="197" border="0">
    <tr>
      <td width="7%"><label for="CenterName">Center Name:* </label></td>
      <td width="75%"><input class="text" type="text" name="CenterName" id="CenterName" /></td>
    </tr>
    <tr>
      <td><label for="Country2">Country*</label></td>
      <td><?php
       
  $query="SELECT CountryId,CountryCode,CountryName FROM Countries";


$result = mysql_query ($query);
echo "<select name=country value=''>Country Name</option>";


while($nt=mysql_fetch_array($result)){
echo "<option value=$nt[CountryId]>$nt[CountryName]</option>";

}

 
echo "</select>"; 
?></td>
    </tr>
    <tr>
      <td><label for="stateID2">State/County*</label></td>
      <td><input class="text" type="text" name="state" id="state" /></td>
    </tr>
    <tr>
      <td><label for="CityID2">City*</label>
&nbsp;</td>
      <td><input class="text" type="text" name="CityName" id="CityName" /></td>
    </tr>
    <tr>
      <td><label for="Address2">Street Address*</label></td>
      <td><textarea name="Address1" id="Address1" cols="45" rows="5"></textarea></td>
    </tr>
    <tr>
      <td>Contact Information</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Center Phone*</td>
      <td><input class="text" type="text" name="PrimaryPhone" id="PrimaryPhone" /></td>
    </tr>
    <tr>
      <td>Center Email*</td>
      <td><input class="text" type="text" name="Email" id="Email" /></td>
    </tr>
    <tr>
      <td>Center Website</td>
      <td><input type="text" class="text" name="Website" id="Website" /></td>
    </tr>
    <tr>
      <td align="center"><input type="submit" name="Submit" id="Submit" value="Submit" /></td>
      <td>&nbsp;</td>
    </tr>
  </table>
</fieldset>
</form>
<p><strong><a href="examreg.php">Exam Centers Registration Homepage</a></strong></p>
<table width= "100%" border="0">
<tr  width= "100%" class="menu">
	<td width= "100%" align= "center"><span><strong>Islamic Online University Exam Centers Registration Portal</strong></span></td></tr>
</table>
 <?
  }






 else {
 header("location: loginerror.php");
 //exit;
             
   }