<?php
#session_start();
include("accesscontrol.php");

  require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
   //require_once('/home/eomanico/public_html/ecampus/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   


    include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password );
$proctorid=$_GET['procid'];

$updqry="DELETE FROM Proctors WHERE ProctorId=$proctorid";
//echo $updqry;
$result= mysql_query($updqry);
 

if($result){
//echo "Delete Succesful";
header("location: showproctor.php");

exit;
}
else{
echo "There was a problem with the operation";
//header("location: u.php");

//exit;
}

?>