<?php
#session_start();
include("accesscontrol.php");
  
  require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   


    include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password );


$BoardId=$_GET['BoardId'];

$result= mysql_query("DELETE FROM NoticeBoard WHERE BoardId=$BoardId");

if($result){

header("location: cancelpost.php");

exit;
}
else{
echo "mail could not be sent";
}