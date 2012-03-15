<?php


include("accesscontrol.php");

  
  require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   


   //unset($_SESSION['sch_page_name']);
    include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password );
 //get the q parameter from URL
$valuecheck=$_GET["q"];  

if($valuecheck=='Suggest page offline')//page is currently offline
{
	$updval=mysql_upd_record( "", "CenterSuggestPage", "status = '0'", "id = 1" );
}
elseif($valuecheck=='Suggest page online')//page is currently online
{
	$updval=mysql_upd_record( "", "CenterSuggestPage", "status = '1'", "id = 1" );
}
   //check for suggest page display status
   $checkpage = mysql_extract_records_where( "", "CenterSuggestPage", "id=1 ", "status" );
//echo $check;
$status=$checkpage[1]['status']; 

if ($status==1)// suggest page offline 
{
 $inputvalue2= "Suggest page offline";
}
   else{// suggest page online
	   $inputvalue2= "Suggest page online";
   
   }
   ?>

<input type="submit" name="suggest" value="<?= $inputvalue2; ?>" onClick="getStatus(this.value)">