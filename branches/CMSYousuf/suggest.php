<?php
/*
$con = mysql_connect("localhost","eomanico_jamal2","enter");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

//mysql_select_db("my_db", $con);


//connect to mysql
//require_once ('config.php');

//select the appropriate db
$mysql=mysql_select_db("eomanico_external",$con);
if(!$mysql)
{
echo 'cannot select database';
exit;
}
*/


 #session_start();
  
   require_once('/home/eomanico/public_html/ecampus/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   


   //unset($_SESSION['sch_page_name']);
    include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password );
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
function EmailValidation($email) { 
    $email = htmlspecialchars(stripslashes(strip_tags($email))); //parse unnecessary characters to prevent exploits
    
    if ( eregi ( '[a-z||0-9]@[a-z||0-9].[a-z]', $email ) ) { //checks to make sure the email address is in a valid format
    $domain = explode( "@", $email ); //get the domain name
        
        if ( @fsockopen ($domain[1],80,$errno,$errstr,3)) {
            //if the connection can be established, the email address is probabley valid
            return true;
            /*
            
            GENERATE A VERIFICATION EMAIL
            
            */
            
        } else {
            return false; //if a connection cannot be established return false
        }
    
    } else {
        return false; //if email address is an invalid format return false
    }
} 


//collect the values from the form

$centername = $_POST['CenterName'];
$email = $_POST['Email'];
$website=$_POST['Website'];
//$firstname=$_POST['firstname'];
$address=$_POST['Address1'];
$phoneno=$_POST['PrimaryPhone'];
$country=$_POST['Country'];
$city=$_POST['CityName'];
$state=$_POST['state'];


	
	
	//Sanitize the POST values
	 $centername = clean($_POST['CenterName']);
	 $email = clean($_POST['Email']);
	$website=clean($_POST['Website']);
	
	$address=clean ($_POST['Address1']);
	$phoneno=clean ($_POST['PrimaryPhone']);
	$country=clean ($_POST['Country']);
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
if(strlen($country)==0)

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

  $errors[]="please specify only numeric characters as your phone number";

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

    include 'centersuggest.php';  

}  

else 

{  
$query= "insert into reg_tab (username, password,  email, first_name, surname, country, town, phone)

values ('" . $username . "', '" . $password . "','" . $email . "','" . $firstname . "','" . $surname . "','" . $country. "' ,'" . $town . "','" . $phoneno . "')";

// Notice the single quotes that enclose

// email and name in the above

// SQL query.
//$error= "The specified email exists in our database";

//$result=mysql_query($query) or die(mysql_error());
$result=mysql_query($query) or die("the database is not responding");
}
if(!mysql_error())
{
header("location: submission_succesful.php");
//echo "Your verification link has been succesfully sent";
}
?>