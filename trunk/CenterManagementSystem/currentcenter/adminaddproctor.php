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

if (isset($_POST['Submit'])) { 

 

//function to ensure the value is numeric

function fnValidateNumber($value) {    

 if(ereg("\+?([0-9]{2})-?([0-9]{3})-?([0-9]{6,7})", $value) || $value == "") {
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

    

    if ( eregi ( '[a-z||0-9]@[a-z||0-9].[a-z]', $email ) ) { 
	return true;
	
	//checks to make sure the email address is in a valid format

    //$domain = explode( "@", $email ); //get the domain name
//
//        
//
//        if ( @fsockopen ($domain[1],80,$errno,$errstr,3)) {
//
//            //if the connection can be established, the email address is probabley valid
//
//            return true;
//
//            /*
//
//            
//
//            GENERATE A VERIFICATION EMAIL
//
//            
//
//            */
//
//            
//
//        } else {
//
//            return false; //if a connection cannot be established return false
//
//        }

    

    } else {

        return false; //if email address is an invalid format return false

    }

} 







//collect the values from the form



$proctorname = $_POST['ProctorName'];

$proctorpost = $_POST['ProctorPost'];

$proctormail = $_POST['ProctorMainEmail'];

$proctormail2 = $_POST['AdditionalEmails'];

$phoneno=$_POST['PrimaryPhone'];

$phone2 = $_POST['AdditionalPhones'];

$centerid=$_POST['centerid'];





	

	//Sanitize the POST values

	 $proctorname = clean($_POST['ProctorName']);

	 $proctorpost = clean($_POST['ProctorPost']);

	$proctormail=clean($_POST['ProctorMainEmail']);

	$proctormail2=clean ($_POST['AdditionalEmails']);

	$phoneno=clean ($_POST['PrimaryPhone']);

	$phone2=clean ($_POST['AdditionalPhones']);

	$centerid=clean ($_POST['centerid']);

	

	

	

	$errors = array();

	

	

	if (!EmailValidation($proctormail))

	{

		$errors[]= "The email appears to be in valid";

	}

 

	if(strlen($proctorname)==0)



{



  $errors[]="Please specify the Proctor's Name";



}

if(strlen($centerid)==0)



{



  $errors[]="Ensure you add a valid Center Id";



}







if(!fnValidateNumber($phoneno))



{



  $errors[]="Number is not valid, please try removing spaces and enter with the ISD code";



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





$query1= "insert into Proctors 

(ProctorName, ProctorPost, ProctorMainEmail, AdditionalEmails, PrimaryPhone, AdditionalPhones, CenterId)



values ('" . $proctorname . "', '" .$proctorpost. "','" . $proctormail . "','" . $proctormail2 . "','" . $phoneno . "', '" . $phone2 . "','" . $centerid. "')";



// Notice the single quotes that enclose



// email and name in the above







$result1=mysql_query($query1) or die("Oops!!! sorry please try again");



if(!mysql_error())

{

//header("location: submission_succesful.php");

echo "Proctor Succesfully Added.Thank you";

}

}

}





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

	<td width= "100%" align= "center"><span><strong>Exam Center Suggestion page</strong></span></td>

      

</tr>

</table>

<? include_once("linkmenu.php");?>



<p>Fill in the Proctors details below and Carefully enter the Associated Center Id </p>



<form id="form1" name="form1" method="post" action="">

  <table width="572" height="314" border="1">

    <tr>

      <td width="141"><label for="ProctorName">Name of the Proctor</label>

*</td>

      <td width="152"><input class="text" type="text" name="ProctorName" id="ProctorName" /></td>

      <td width="103">&nbsp;</td>

      <td width="148">&nbsp;</td>

    </tr>

    <tr>

      <td><label for="ProctorPost2">Post of the Proctor </label></td>

      <td><input class="text" type="text" name="ProctorPost" id="ProctorPost" /></td>

      <td>&nbsp;</td>

      <td>&nbsp;</td>

    </tr>

    <tr>

      <td height="53">Contact Information</td>

      <td>&nbsp;</td>

      <td>&nbsp;</td>

      <td>&nbsp;</td>

    </tr>

    <tr>

      <td>Email*</td>

      <td><input class="text" type="text" name="ProctorMainEmail" id="ProctorMainEmail" /></td>

      <td>Additional Email</td>

      <td><input class="text" type="text" name="AdditionalEmails" id="AdditionalEmails" /></td>

    </tr>

    <tr>

      <td>Phone*</td>

      <td><input class="text" type="text" name="PrimaryPhone" id="PrimaryPhone" /></td>

      <td>Additional Phone</td>

      <td><input class="text" type="text" name="AdditionalPhones" id="AdditionalPhones" /></td>

    </tr>

    <tr>

      <td><label for="centerid2">Center Number*</label></td>

      <td><?php

       

  $query="SELECT CenterId,CenterName FROM ExamCenters";





$result = mysql_query ($query);

echo "<select name=centerid value=''>Center Id</option>";



echo "<option value=''>Choose One</option>";

while($nt=mysql_fetch_array($result)){

echo "<option value=$nt[CenterId]>$nt[CenterId]</option>";



}



 

echo "</select>"; 

?></td>

      <td>&nbsp;</td>

      <td>&nbsp;</td>

    </tr>

  </table>

  <p>

    <input type="submit" name="Submit" id="Submit" value="Submit" />

  </p>

<p>&nbsp; </p>

  <p>&nbsp;</p>

</form>



<table width= "100%" border="0">

<tr  width= "100%" class="menu">

	<td width= "100%" align= "center"><span><strong>Islamic Online University Exam Centers Registration Portal</strong></span></td></tr>

 </table>