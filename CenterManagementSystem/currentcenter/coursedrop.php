<?php

require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
   //require_once('/home/eomanico/public_html/ecampus/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   
//include( "cfg.php" );
include( "centerconn.php" );
   include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
  // @include( "../lang/en_utf8/countries.php" );
$mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password );   
//$mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password );
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



 if ( $USER->id ) { 
 
 $x=$USER->id;
 $sql="SELECT DISTINCT u.id
FROM mdl_role_assignments ra, mdl_user u, mdl_course c, mdl_context cxt
WHERE ra.userid = u.id
AND ra.contextid = cxt.id
AND cxt.contextlevel =50
AND cxt.instanceid = c.id
AND (
roleid =5
OR roleid =3
)
AND u.id=$x";

$result=mysql_query($sql);
$rows=mysql_num_rows($result);
//echo $rows;
 
while ($record=mysql_fetch_array($result)){ //$value=$record['id'];

$y=$record['id'];

}
if ($x==$y){



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="http://bais.islamiconlineuniversity.com/bais/theme/standard/styles.php" />
<link rel="stylesheet" type="text/css" href="http://bais.islamiconlineuniversity.com/bais/theme/ingenuous/styles.php" />
<meta name="description" content="" />
<!--[if IE 7]>
		    <link rel="stylesheet" type="text/css" href="http://bais.islamiconlineuniversity.com/bais/theme/standard/styles_ie7.css" />
		<![endif]-->
<!--[if IE 6]>
		    <link rel="stylesheet" type="text/css" href="http://bais.islamiconlineuniversity.com/bais/theme/standard/styles_ie6.css" />
		<![endif]-->
<meta name="keywords" content="moodle, Islamic Online University - BAIS " />
<title>Islamic Online University - BAIS</title>
<link rel="shortcut icon" href="http://bais.islamiconlineuniversity.com/bais/theme/ingenuous/favicon.ico" />
<!--<style type="text/css">/*<![CDATA[*/ body{behavior:url(http://bais.islamiconlineuniversity.com/bais/lib/csshover.htc);} /*]]>*/</style>-->
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
<link href="css/date-css.css" rel="stylesheet" type="text/css">
<script type='text/javascript' src='js/zapatec.js'></script>
<script type="text/javascript" src="js/calendar.js"></script>
<script type="text/javascript" src="js/calendar-en.js"></script>
</head>
<body class="course course-1 notloggedin dir-ltr lang-en_utf8" id="site-index" width="100%">
<div id="page" width="100%">
<div id="header-home" class=" clearfix" width="100%">
  <div class="wrapper clearfix"> <img class="logo" src="http://bais.islamiconlineuniversity.com/bais/theme/ingenuous/pix/logobanner.png"/>
    <div class="headermenu">
      <div class="logininfo"></div>
    </div>
  </div>
  <div id="menubox" class="wrapper clearfix">
    <ul id="menu">
      <li><a href="http://bais.islamiconlineuniversity.com/bais" title="Home">Home</a></li>
      <li><a href="http://bais.islamiconlineuniversity.com/bais/course/index.php" title="Courses">Courses</a></li>
      <li><a href="http://bais.islamiconlineuniversity.com/bais/calendar/view.php?view=month" title="Calendar">Calendar</a></li>
    </ul>
  </div>
</div>
<div class="background" width="100%">
<table border="4" cellpadding="0" cellspacing="0" align="center" width="100%">


<tr> 
<td class="headerblue"><h2>STUDENT COURSE DROP REQUEST</h2></td>
</tr></table>
<form id="form1" name="form1" method="post" action="">
<table width="572"  border="0">
    <tr>
      <td width="141"><label for="tamail">The TA's email</label>
*</td>
      <td width="152"><input class="text" type="text" name="tamail" id="tamail" /></td>
      <td width="103">&nbsp;</td>
      <td width="148">&nbsp;</td>
    </tr>
    <tr>
      <td><label for="ProctorPost2">Choose the Course </label></td>
      <td>
	  <?

 $query= "SELECT DISTINCT mc.shortname, mi.course_id
FROM mdl_course mc, mdl_i_usercourse_hst mi
WHERE mc.id = mi.course_id";

/* You can add order by clause to the sql statement if the names are to be displayed in alphabetical order */

$result = mysql_query ($query);
echo "<select name=courseid value=''>Course Name</option>";
echo "<option value=''> Choose  </option>";
// printing the list box select command

while($nt=mysql_fetch_array($result)){//Array or records stored in $nt
echo "<option value=$nt[course_id]>$nt[shortname]</option>";
/* Option values are added by looping through the array */
}
  
echo "</select>";// Closing of list box   


 

?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    
      </table>
  <p>
    <input type="submit" name="Submit" id="Submit" value="Submit" />
  </p>

</form>
                    <?
					if (isset($_POST['Submit'])) { 
					
					
					
					$tamail=$_POST['tamail'];
					$cid=$_POST['courseid'];
					
				$studentid=$USER->id;
				$firstname=$USER->firstname;
				$lastname=$USER->lastname;
				$email=$USER->email;
				
$firstname=mysql_real_escape_string($USER->firstname);
$lastname=mysql_real_escape_string($USER->lastname);
$email=mysql_real_escape_string($USER->email);
$tamail=mysql_real_escape_string($tamail);

$errors = array();
	
	if (!EmailValidation($tamail))
	{
		$errors[]= "The email appears to be in valid";
	}
	
 
	
if(strlen($cid)==0)

{

  $errors[]="Please choose the course you wish to drop";

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
else{


		$query= "insert into DropRequest (userid,firstname,lastname,email,dropdate,droptime,course,tamail) values ($studentid,'".$firstname."', '".$lastname."', '".$email."',NOW(),CURTIME(), $cid ,'".$tamail."')";
		echo $query;
		$dropres=mysql_query($query);
		if ($dropres){echo "Request succesfully sent";}
		else{echo "There was a problem with your request";}
				
}
					
					
                    }
 }
 
 }
else {
	//echo "You aint logged in fella..";
 header("location: permerror.php");
 //exit;
             
   }



