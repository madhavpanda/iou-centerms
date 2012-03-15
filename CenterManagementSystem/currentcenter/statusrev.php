<? #session_start();
include("accesscontrol.php");
  
   require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   


   //unset($_SESSION['sch_page_name']);
    //include( "centerconn.php" );
    include( "cfg.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   //$mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password );
   $mysql_connect_id = mysql_start( $mysql_server, $moodle_db, $mysql_username, $mysql_password );
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
	<td width= "100%" align= "center"><span><strong>Center Request Status view</strong></span></td>
      
</tr>
</table>
<? include_once("linkmenu.php");?>
<table width= "100%" border="0">
<tr width= "100%" bgcolor="#000000" class="td_data">
     
    <td  width= "50%"><span><strong>Requested By</strong></span></td>
    <td  width= "30%"><span><strong>Email</strong></span></td>
    
    <td  width= "20%"><span><strong>Approve</strong></span></td>
    
    
  </tr>	

<?php

$whoid=$_GET['who'];
$id=$_GET['who'];
$centid=$_GET['centid'];
$nuid=$_GET['centid'];
//$qry="Select $db1.mdl_user.id,$db1.mdl_user.firstname,$db1.mdl_user.lastname,$db1.mdl_user.email from $db1.mdl_user where $db1.mdl_user.id= $whoid";
$qry="Select id,firstname,lastname,email from mdl_user where id= $whoid";


//echo $qry;

$getwho = mysql_query($qry);
$getwho2 = mysql_query($qry);


//echo "here it is ".$getwho;
$dec=mysql_fetch_array($getwho2);
if(empty($dec)){echo "No Records for this User was found.It might have been entered by the admin".'<br />';?>
<a href="adminconfirm.php?nuid=<?=$nuid ?>" onclick="return popitup('adminconfirm.php?nuid=<?=$nuid ?>')">Approve here</a>
<?
}

while($rec=mysql_fetch_array($getwho)){
//echo $whorec;


$firstname=$rec['firstname'];
$lastname=$rec['lastname'];
$mail=$rec['email'];



?>
<tr class="td_data" >
     
    
  <td> <?php echo $firstname.' '.$lastname; ?> </td>
    <td> <?php print $mail ?> </td> 
    <td> <a href="confirmcenter.php?mail=<?=$mail ?>&&centid=<?=$centid ?>" onclick="return popitup('confirmcenter.php?mail=<?=$mail ?>&&centid=<?=$centid ?>')">Confirm center Approval</a> </td>
    
   
  </tr>
<?
}


 

?>
</table>
<script>
	
		
function popitup(url) {
	newwindow=window.open(url,'name','height = 380, width = 550,left=350,top=200');
	if (window.focus) {newwindow.focus()}
	return false;
}

</script>