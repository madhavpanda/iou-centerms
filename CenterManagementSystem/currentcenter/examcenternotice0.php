<?php
 

    require_once('/home/eomanico/public_html/ecampus/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   
include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password ); 
 #session_start();
 ?>
 <link rel="shortcut icon" href="http://bais.islamiconlineuniversity.com/bais/theme/ingenuous/favicon.ico" />
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
	<td width= "100%" align= "center"><span><strong>Exams Center Notice Board</strong></span></td>
      
</tr>
</table>


<?php

$boardqry="select * from NoticeBoard order by PostTime";
$boardres=mysql_query($boardqry);


while($boardrec=mysql_fetch_array($boardres)){
	$topic=$boardrec['Topic'];
	$category=$boardrec['Label'];
	$message=$boardrec['Message'];
	$date=$boardrec['PostTime'];
	
 
?>
<table width="100%" border="0">
<tr class="td_data" width="100%">
     
     
     <td width="100%"><?php echo $topic ; ?> <br> by Center Manager - <?php echo $date ; ?></td>
    
   
  </tr>	
  <tr bgcolor="#999999" class="td_data" width="100%">
     
     <td colspan="10" width="100%" bgcolor="#999999"><span><strong><? echo $message; ?></strong></span></td>
   
   
  </tr>	
</table> 
   
<?
 }   

 
 ?>
 
 <table width= "100%" border="0">

<tr  width= "100%" bgcolor="#000000" class="td_data">
	<td width= "100%" align= "center"><span><strong>Islamic Online University Exam Centers Registration Portal</strong></span></td></tr>
	
 </table>
 