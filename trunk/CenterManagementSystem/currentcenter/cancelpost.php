<?php

include("accesscontrol.php");

 require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
    //require_once('/home/eomanico/public_html/ecampus/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   
include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password ); 
 #session_start();
 ?>
  <link rel="stylesheet" href="http://www.eomani.com/ecampus/center/CenterCMS.css" />
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
<title>Islamic Online University Exam Centers Registration</title>
<table width= "100%" border="0">
<tr  width= "100%" >
	<td width= "100%" align= "center" class="menu"><span><strong>Exams Center Notice Board</strong></span></td>
      
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
<center>
<table width="85%" border="0">
<tr width="100%">
     
     
     <td width="100%" ><h3><?php echo $topic ; ?></h3>
     <em><strong>-- Center Manager</strong></em> <?php echo $PostTime ; ?></td>
    
   
  </tr>	
  <tr class="data"  width="100%">
     
     <td colspan="10" width="100%" ><blockquote>
       <p><span ice:repeating="true">&nbsp;&nbsp;<? echo $message; ?></span></p>
     </blockquote></td>
   <td> <form id="deleteNotice" method="POST" action="deletepost.php?BoardId=<?php echo $boardrec['BoardId']; ?>">
	<input type="submit" name="DeletePost" value="Delete" />
    </form>
       
     </td>
   
  </tr>	
</table> 
   
<?
 }   

 
 ?>
 </center>
 <br/>
 <br/>
 <br/>
 <table width= "100%" border="0">

<tr  width= "100%" class="menu">
   <td width= "100%" align= "center" ><span><strong>Islamic Online University Exam Centers Registration Portal</strong></span></td></tr>
	
 </table>
 