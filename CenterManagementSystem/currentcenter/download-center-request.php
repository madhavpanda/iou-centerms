<?php
session_start();
if (!session_is_registered('id') || !session_is_registered('adminname'))
{
	header("Location: /admincms/login.php");
}

$acces_type = explode(", ",$_SESSION['access_type']);

if($_SESSION['access_type'] == "all")
{
	// Do nothing
	
}
else
{
	if(!in_array("Center Info", $acces_type))
	{
		header("Location: /admincms/index.php");
	}
}

require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   


   //unset($_SESSION['sch_page_name']);
    include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password );

 $title = "Name\tEmail\tCenter Name\tCity\tCountry\tStatus";

$query="select * from CentreRequest order by CenterName";
$result=mysql_query($query);

 while ( $record  = mysql_fetch_array($result, 1)){
 $firtsname=$record['FirstName'];
  $lastname=$record['LastName'];
 $centername=$record['CenterName'];
 $city=$record['City'];
 $country=$record['Country'];
  $email=$record['Email'];
 


  
      

		$line .=   $record['FirstName'] . " " . $record['LastName']."\t"; 
		$line .= $record['Email'] ."\t"; 
		$line .=    $record['CenterName'] ."\t"; 
		
		$line .=    $record['City'] ."\t";
		$line .=    $record['Country'] ."\t";
		
	
 $statcheck= "select * from CentreRequest where email= '".$record['Email']."' " ;
       $checkres=mysql_query($statcheck);
       while ( $rec = mysql_fetch_array($checkres, 1)){
 $status=$record['status'];}
 //echo $status;
 if($status != 0)
 { $line .=   "Notified"."\t"; }
 else
 {$line .= "Yet to be Notified"."\t";}
 



//}
	 
		
$line .= "\n";


		}

?>
<?php

 header("Content-type: application/x-msexcel"); 
header("Content-Disposition: attachment; filename=center-request-".date("YmdHis").".xls"); 
header("Pragma: no-cache"); 
header("Expires: 0"); 
echo "$title\n$line"; 

?>