<?php

 include("accesscontrol.php");


    require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
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

$message = $_POST['message'];
$topic = $_POST['topic'];
$category = $_POST['category'];



	
	//Sanitize the POST values
	 $message = clean($_POST['message']);
	 $topic = clean($_POST['topic']);
	$category=clean($_POST['category']);
	
	
	
	$errors = array();
	
	
	
 
	if(strlen($category)==0)

{

  $errors[]="Please indicate a category";

}
if(strlen($topic)==0)

{

  $errors[]="Oops Mangager you forgot to add a Topic!!!";

}

if(strlen($message)==0)

{

  $errors[]="Hmmn Mangager are you sure you want to post No message!!! :)";

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


$query1= "insert into NoticeBoard 
(Label, Topic, Message, PostTime)

values ('" . $category . "', '" .$topic. "','" . $message . "' ,CURDATE())";

// Notice the single quotes that enclose

// email and name in the above



$result1=mysql_query($query1) or die("Oops!!! sorry please try again");

if(!mysql_error())
{
//header("location: submission_succesful.php");
echo "Notice Succesfully Added.Thank you";
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
<tr  width= "100%" class="menu">
	<td width= "100%" align= "center"><span><strong>Notice Board Update</strong></span></td>
      
</tr>
</table>
<? include_once("linkmenu.php");?>

<p>Fill in the neccesary details below</p>

<form id="form1" name="form1" method="post" action="">
  <p>
    <label for="category">Category</label><br>
       	
    <input name="category" type="text" id="category" size="50" maxlength="1000" />
  </p>
  <p>
    <label for="topic">Topic<br>
    </label>
    <input name="topic" type="text" id="topic" size="50" maxlength="1000">
  </p>
  <p>
    <label for="textarea">Message<br>
    </label>
    <textarea name="message" id="message" cols="60" rows="8"></textarea>
  </p>
  <p>
    <input type="submit" name="Submit" id="Submit" value="Submit" />
  </p>
  
</form>

<table width= "100%" border="0">
<tr><td> <a href=examcenternotice.php>Checkpost</a> &nbsp;&nbsp;&nbsp; <a href=cancelpost.php>Delete post</a>
</td></tr>
<tr  width= "100%"  class="menu">
	<td width= "100%" align= "center"><span><strong>Islamic Online University Exam Centers Registration Portal</strong></span></td></tr>
</table>