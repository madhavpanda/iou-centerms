<? #session_start();
include("accesscontrol.php");

  
   require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   


   //unset($_SESSION['sch_page_name']);
    include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password );
   
   ?>
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
<tr  width= "100%" bgcolor="#000000" class="td_data">
	<td width= "100%" align= "center"><span><strong>Exam Centers Approval Confirmation</strong></span></td>
      
</tr>
</table>
<?
$x=$_GET['mail'];

$centid=$_GET['centid'];
$updcent= "Update ExamCenters set Active=1 where CenterId=$centid";

//echo $updcent;
$updres=mysql_query($updcent);

if ($updres){


$message = "Salamalaykum.<br><br>

		This is to notify you that the center you suggested have been approved.
		You may now proceed to the Registration page and make a fresh request for the Center.
		
		Wish you all the best in your forthcoming exams.
		
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
			$subject="Suggested Center Approved!";
			
			$headers .= 'From: '.$sender_name. '<'.$sender_email.'>' . "\n";
			
			$result = mail($x, $subject, $message, $headers);
			
			if ($result)
			{echo "Update successful!!!.The Center will now appear as  Approved";}
			
		
			





}
else{
echo "There was a problem with the Update.If this continues please contact the aadministrator";

}
?>