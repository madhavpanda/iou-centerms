<?php
	$mysql_server = "localhost";
//echo $_SERVER['HTTP_HOST'];
	if ( $_SERVER['HTTP_HOST'] == "localhost" ) {
   	 $mysql_username = "eomanico_mdle1";
   	$mysql_password = "RrcVN7MreXdJ";
   } else {
   	$mysql_username = "eomanico_mdle1";
   	$mysql_password = "RrcVN7MreXdJ";
   }

   $moodle_db = "eomanico_mdle1";
   $moodle_user_tb = "mdl_user";
   $moodle_user_extra_data_tb = "mdl_user_info_data";
   $moodle_payments_tb = "moodle_payments";
?>
