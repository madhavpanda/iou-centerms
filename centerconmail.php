<?php
#session_start();
  
   require_once('/home/eomanico/public_html/ecampus/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   


   //unset($_SESSION['sch_page_name']);
    include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password );
//this section recieves the input email and seraches for the corresponding first name

//get the email
//echo $_GET[$record['email']];
$x= $_GET['mail'];
$fname=$_GET['fname'];
$y= $_GET['mail'];
$statupd= "update CentreRequest set status =1 where email= '".$y."' ";
		 //echo $statupd;
		// $statres=mysql_query($statupd);
		 $statres = mysql_query($statupd);


//$mail="arunajamal@yahoo.com";
/*
// Get the first name from the mdl_user table
$query= "select firstname from mdl_user where email='$x'";
$result = mysql_query($query);
$row = mysql_fetch_assoc($result);
//echo $row['firstname'];
$fname=$row['firstname'];
//echo "my first name is $fname";
*/

//This section makes a copy of the text file to be ammended and emailed
$source = 'exam_center_confirmation_email.txt';
$destination = 'examcentermail_destination.txt';

$data = file_get_contents($source);

$handle = fopen($destination, "w");
fwrite($handle, $data);
fclose($handle);


// This section replaces the first name header with string from the db



//read the entire string
//if ()
$str=implode("\n",file($destination));

$fp=fopen($destination,'w');
//replace something in the file string - this is a VERY simple example
$str=str_replace('_first_name_ _last_name_',$fname,$str);

//now, TOTALLY rewrite the file
fwrite($fp,$str,strlen($str));


//send the mail
//$to=$email;

$subject = "Exam Center Confirmation";

$from = "centers@islamiconlineuniversity.com";
$headers = "From: $from";
$sentmail = mail($x,$subject,$str,$headers);
// if your email succesfully sent
if($sentmail){

header("location: stdreqview.php");
//include 'stdreqview.php';
exit;

//echo "Exam center confirmation mail has been succesfully sent";
//include 'stdreqview.php';
}
else {
//header("location: submission_error.html");
echo "Please contact the Administrator";
}
/*
else{
echo "mail could not be sent";
}
*/
?>