<? #session_start();
  
  include("accesscontrol.php");
   require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
  
  
  // require_once('/home/eomanico/public_html/ecampus/config.php');
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
	<td width= "100%" align= "center"><span><strong>Proctor details</strong></span></td>
      
</tr>
</table>
<? include_once("linkmenu.php");?>
<table>
<tr bgcolor="#000000" class="td_data">
     
     <td width="11%"><span><strong>Proctor Name</strong></span></td>
    <td  width="9%" style="color:#FFFFFF"><strong>Post</strong></td>
    <td  width="12%"><span><strong>Email</strong></span></td>
    <td  width="12%"><span><strong>Phone no</strong></span></td>
    <td  width="10%"><span><strong>Status</strong></span></td>
    <td  width="5%"><span><strong>Center</strong></span></td>
    <td  width="5%"><span><strong>Edit</strong></span></td>
  </tr>	
<?php
$centerid=$_GET['id'];
$centname=$_GET['name'];


   
$centerproctor= "Select p.ProctorId, p.ProctorName, p.ProctorPost,p.ProctorMainEmail,p.AdditionalEmails,p.PrimaryPhone,p.Active from  ExamCenters e join Proctors  p on 
p.CenterId=e.CenterId where p.CenterId=$centerid";
//echo $centerproctor;
$getproctor = mysql_query($centerproctor);
//echo $getproctor;
while($procrec=mysql_fetch_array($getproctor)){

$name=$procrec['ProctorName'];
$post=$procrec['ProctorPost'];
$mail=$procrec['ProctorMainEmail'];
$mail2=$procrec['AdditionalEmails'];
$phoneno=$procrec['PrimaryPhone'];
if(empty($phoneno)){$phone="Nil";}
else{$phone=$phoneno;}


$extphone=$procrec['AdditionalPhones'];
if(empty($extphone)){$phone2="Nil";}
else{$phone2=$extphone;}
$active=$procrec['Active'];
if ($active==0){
 
 $status="<a href='statusrev.php'>yet to be Approved</a>";}
 else{$status="Approved";}
?>

<tr class="td_data" >
     
    
    <td> <?php echo $name; ?> </td>
    <td> <?php print $post ?> </td> 
    <td> <?php print $mail ?> </td>
    <td> <?php print '1.'.$phone.'<br />'.'2.'.$phone2 ?> </td>
    
    <td> <?php echo $status; ?> </td>
    <td> <?php echo $centname ;  ?> </td>
   <td><form id="Update" method="POST" action="updateproctor.php?proctorid=<?php echo $procrec['ProctorId'];?>">
	<input type="submit" name="SendProc" value="Edit" />
    </form></td>
  </tr>

<?php

}   

?>
</table>