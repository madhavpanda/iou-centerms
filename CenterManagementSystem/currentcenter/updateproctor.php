<?php
 
include("accesscontrol.php");
   //require_once('/home/eomanico/public_html/ecampus/config.php');
   require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
   
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   
include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password ); 
 #session_start();
 
 //Fetch Values from Querystring
 
 $ProctorId=$_GET['proctorid'];
 $ProctorDetails = mysql_query("Select * from Proctors where ProctorId=$ProctorId");
 $record=mysql_fetch_array($ProctorDetails);
 $proctorname = $record['ProctorName'];
 $proctorid = $record['ProctorId'];
 $proctorpost = $record['ProctorPost'];
 $proctormainemail = $record['ProctorMainEmail'];
 $additionalemails= $record['AdditionalEmails'];
 $primaryphone= $record['PrimaryPhone'];
 $additionalphones= $record['AdditionalPhones'];
 $active= $record['Active'];
 $centerid=$record['CenterId'];


if (isset($_POST['Submit'])) { 
 
//function to ensure the value is numeric
function fnValidateNumber($value) {    
 #is_ double($value);    
  #is_ float($value);     
  #is_ int($value);     
  #is_ integer($value);     
  return is_numeric($value); } 

//Function to sanitize values received from the form. Prevents SQL injection
	function clean($str) {
		$str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);
	}

//function to sanitize email
function EmailValidation($proctormainemail) { 
    $proctormainemail = htmlspecialchars(stripslashes(strip_tags($proctormainemail))); //parse unnecessary characters to prevent exploits
    
    if ( eregi ( '[a-z||0-9]@[a-z||0-9].[a-z]', $proctormainemail ) ) {
		//echo "Here I am ";
		return true;
		 //checks to make sure the email address is in a valid format
   // $domain = explode( "@", $email ); //get the domain name
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
//    
    } else {
        return false; //if email address is an invalid format return false
    }
} 



//collect the values from the form
$proctorname = $_POST['ProctorName'];
 
 $proctorpost = $_POST['ProctorPost'];
 $proctormainemail = $_POST['ProctorMainEmail'];
 $additionalemails= $_POST['AdditionalEmails'];
 $primaryphone= $_POST['PrimaryPhone'];
 $additionalphones= $_POST['AdditionalPhones'];
 $active= $_POST['Active'];
 $centerid=$_POST['CenterId'];

	
	//Sanitize the POST values
    $proctorname = clean($_POST['ProctorName']);
   
 	$proctorpost = clean($_POST['ProctorPost']);
 	$proctormainemail = clean($_POST['ProctorMainEmail']);
 	$additionalemails= clean($_POST['AdditionalEmails']);
 	$primaryphone= clean($_POST['PrimaryPhone']);
 	$additionalphones= clean($_POST['AdditionalPhones']);
 	$active= clean($_POST['Active']);
 	$centerid=clean($_POST['CenterId']);
	
	
	$errors = array();
	
	
	if (!EmailValidation($proctormainemail))
	{
		$errors[]= "The email appears to be in valid";
	}
 
	if(strlen($proctorname)==0)

{

  $errors[]="Please specify a Proctor Name";

}

if(strlen($proctormainemail)==0)

{

  $errors[]="Please provide the Proctor's Email Address";

}
if(strlen($centerid)==0)

{

  $errors[]="The Center ID was not specified.";

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

$query1= "update `Proctors` SET `ProctorName`='".$proctorname."',`ProctorPost`='".addslashes($proctorpost)."',`ProctorMainEmail`='".$proctormainemail."',`AdditionalEmails`='".addslashes($additionalemails)."',`PrimaryPhone`= '".addslashes($primaryphone)."',`AdditionalPhones`= '".addslashes($additionalphones)."',`Active`='".$active."' Where `ProctorId` = $ProctorId";
echo $query1;

$result1=mysql_query($query1) or die("Oops!!! sorry please try again");

if(!mysql_error())
{
//header("location: submission_succesful.php");
echo "Operation Succesfull.The proctor has been updated to version 2.0.1.4.3.8 ok i was just kidding, I updated what you told me";
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
	<td width= "100%" align= "center" ><span><strong>Proctor Details Update page</strong></span></td>
      
</tr>
</table>
<? include_once("linkmenu.php");?>

<p>Edit the Centers details below </p>

<form id="form1" name="form1" method="post" action="">
  <fieldset>
   <legend>Proctor Information</legend>
   <table width="726" height="465" border="0">
    <tr>
      <td width="123">Proctor Name:*</td>
      <td width="587"><input class="text" type="text" name="ProctorName" id="ProctorName" value="<?php echo $proctorname; ?>" /></td>
    </tr>
    <tr>
      <td width="123">Proctor Post:*</td>
      <td width="587"><input class="text" type="text" name="ProctorPost" id="ProctorPost" value="<?php echo $proctorpost; ?>" /></td>
    </tr>
    <tr>
      <td width="123">Main Email:*</td>
      <td width="587"><input class="text" type="text" name="ProctorMainEmail" id="ProctorMainEmail" value="<?php echo $proctormainemail; ?>" /></td>
    </tr>
    <tr>
      <td width="123">Additional Emails</td>
      <td width="587"><input class="text" type="text" name="AdditionalEmails" id="AdditionalEmails" value="<?php echo $additionalemails; ?>" /></td>
    </tr>
    <tr>
      <td width="123">PrimaryPhone</td>
      <td width="587"><input class="text" type="text" name="PrimaryPhone" id="PrimaryPhone" value="<?php echo $primaryphone; ?>" /></td>
    </tr>
    <tr>
      <td width="123">AdditionalPhones:</td>
      <td width="587"><input class="text" type="text" name="AdditionalPhones" id="AdditionalPhones" value="<?php echo $additionalphones; ?>" /></td>
    </tr>
   <tr>
      <td width="123">CenterId:*</td>
      <td width="587"><input class="text" type="text" name="CenterId" id="CenterId" value="<?php echo $centerid; ?>" /></td>
    </tr>
     <tr>
                                    <td width="25%" class="text1">Active?</td>
                                    <td width="75%"><select name="Active" id="Active" >
                                        <option value="1" <? if($active == "1"){echo "selected";}?>>Active</option>
                                        <option value="0" <? if($active == "0"){echo "selected";}?>>InActive</option>
                                      </select></td>
                                  </tr>
    <tr>
      <td height="96" align="left" valign="center"><input  type="submit" name="Submit" id="Submit" value="Submit" /></td>
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