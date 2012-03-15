<?php
   #session_start();
   #$_SESSION['project_reg_status_UserProfile_UserName'] = "facilitator";
  include_once("../db/dbconn.php");


    require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
   // require_once('/home/eomanico/public_html/ecampus/config.php');

   require_once($CFG->dirroot .'/course/lib.php');

   require_once($CFG->dirroot .'/lib/blocklib.php');

   

include( "centerconn.php" );

	include( "php_lib3/misc.php" );

   include( "php_lib3/mysql.php" );

   

   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password ); 

/* Include function file */
include_once("function/function.php");
$line .="<table border='1'>";!
	 $sqlfortopic1 = mysql_query("SELECT `ProctorName`,`ProctorMainEmail`,`AdditionalEmails`,`CenterName`,`Email` FROM ExamCenters LEFT OUTER JOIN Proctors ON `ExamCenters`.CenterId=`Proctors`.CenterId WHERE `ExamCenters`.Active =1;");
	 echo mysql_error();
$line .= "<tr><td>ProctorName</td><td>ProctorMainEmail</td><td>AdditionalEmails</td><td>CenterName</td><td>Email</td></tr>";
?>
<?

	while($topic = mysql_fetch_array($sqlfortopic1))
														{	
		
			$line .="<tr>";
	    $line .= "<td>".$topic['ProctorName']."</td>"; 
		$line .= "<td>".$topic['ProctorMainEmail']."</td>"; 
		$line .=  "<td>".$topic['AdditionalEmails']."</td>"; 
		$line .=    "<td>".$topic['CenterName']."</td>"; 
		$line .=     "<td>".$topic['Email'] ."</td>"; 
		
		
		$line .= "</tr>";
		}
		$line .="</table>";
?>
<?php
   
header("Content-type: application/x-msexcel"); 
header("Content-Disposition: attachment; filename=prcotor-info-".date("YmdHis").".xls"); 
header("Pragma: no-cache"); 
header("Expires: 0"); 
echo "$title\n$line"; 
?>