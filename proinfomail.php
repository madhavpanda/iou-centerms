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
$center=$_GET['cent'];
//echo $center;
$proinfo = "Select e.CenterName, p.ProctorName, p.ProctorPost, p.ProctorMainEmail, p.PrimaryPhone 
from ExamCenters e join Proctors p on p.CenterId = e.CenterId where e.CenterName='".$center."'";

//echo $proinfo;

 $prores = mysql_query($proinfo);
 //echo  $prores;
 while ($prorec= mysql_fetch_array($prores)){
	 
	 $proctorname= $prorec['ProctorName'];
	 //echo $proctorname;
	 $proctorpost= $prorec['ProctorPost'];
	 $proctormail= $prorec['ProctorMainEmail'];
	 $proctorfone= $prorec['PrimaryPhone'];
	 /*
	 Proctor Name : $proctorname.<br>
		Proctor Post : $proctorpost.<br>
		Proctor Email : $proctormail.<br>
		Proctor Phone Number: $proctorfone
	 
	 */
 
 }

		$message = "Salamalaykum.<br><br>

		Your Proctor information are as given below :<br><br>
		Proctor Name : $proctorname.<br>
		Proctor Post : $proctorpost.<br>
		Proctor Email : $proctormail.<br>
		Proctor Phone Number: $proctorfone.
		
		<br><br>

		This is an automatically generated email. If you have any concerns
		regarding your Exams centers you may contact the Manager at
		centers@islamiconlineuniversity.com.<br><br>

		
		JazakAllah Khair.<br><br>

		Wassalam";
	
			//---------------------------------------------------------
			//exit;
			//$to = $possible_record[1][email];
			//$name = $possible_record[1][firstname]." ".$possible_record[1][lastname];

			$headers  = 'MIME-Version: 1.0' . "\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
			$sender_name="Center Manager";
			$sender_email="centers@islamiconlineuniversity.com";
			$subject="Proctor Information";
			
			$headers .= 'From: '.$sender_name. '<'.$sender_email.'>' . "\n";
			
			$result = mail($x, $subject, $message, $headers);
			
		
			if($result){

header("location: stdreqview.php");
//include 'stdreqview.php';
exit;
}
else{
echo "mail could not be sent";
}