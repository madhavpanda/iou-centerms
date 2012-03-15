<? #session_start();
  
  include("accesscontrol.php");
  require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
   //require_once('/home/eomanico/public_html/ecampus/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   


   //unset($_SESSION['sch_page_name']);
    include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password );
 // if ( $USER->id ) { 
   ?>
<link rel="shortcut icon" href="http://bais.islamiconlineuniversity.com/bais/theme/ingenuous/favicon.ico" />
<link rel="stylesheet" href="CenterCMS.css" />
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
a:link { 
        color:#FFF;
	text-decoration: none;
}
a:visited { 
        color:#FFF;
	text-decoration: none;
}
a:hover { 
        color:#FFF;
	text-decoration: underline;
}
a:active { 
        color:#FFF;
	text-decoration: none;
}
-->
</style>
<title>List of Proctors</title>
<table width= "100%" border="0">
<tr  width= "100%" class="menu">
	<td width= "100%" align= "center"><span><strong>Lists of Proctors</strong></span></td>
      
</tr>
</table>
<table>
<tr class="menu">
     <td width="5%" bgcolor="#FFFFFF">&nbsp;</td>
     <td width="11%"><span><strong>Name</strong></span></td>
    <td  width="9%"><span><strong>Post</strong></span></td>
    <td  width="12%"><span><strong>Email</strong></span></td>
    <td  width="12%"><span><strong>Phone</strong></span></td>
    <td  width="12%"><span><strong>Center Id</strong></span></td>
    <td  width="25%"><span><strong>Remove Proctor</strong></span></td>
    
    <td  width="11%"><span><strong>Update </strong></span></td>
    <td width="5%" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>	
<?php

   
$getproc= "Select * from Proctors order by CenterId";
//echo $getcenter;
$getres = mysql_query($getproc);
while($record=mysql_fetch_array($getres)){
	$name=$record['ProctorName'];
	$centerid=$record['CenterId'];
	//get the center name
	$centerqry= "Select * from ExamCenters where CenterId=$centerid";
	;
	
	$getcenter=mysql_query($centerqry);
	$countryrec=mysql_fetch_array($getcenter);
		$centername=$countryrec['CenterName'];
	
	$post=$record['ProctorPost'];
 $active=$record['Active'];
 

 $procmail=$record['ProctorMainEmail'];
 $procmail2=$record['AdditionalEmails'];
 

 $procphone=$record['PrimaryPhone'];
 $procphone2=$record['AdditionalPhones'];
 
?>
 <tr bgcolor="#C9C9C9" >
     
    <td bgcolor="#FFFFFF">&nbsp;  </td>
    <td> <?php echo $name;  ?> </td>
    <td> <?php print $post ?> </td> 
    <td> <?php print "1.".$procmail .'<br />'."2.".$procmail2 ?> </td>
    <td> <?php   print "1.".$procphone .'<br />'."2.".$procphone2  ?> </td>
    <td> <?php print  $centerid /* .'<br />'.$centername*/ ?> </td>
    <td> <a href="removeproc.php?pid=<? echo $record['ProctorId']; ?>" target="_blank"> Remove Proctor</a> </td>
    
    <td> <form id="Update" method="POST" action="updateproctor.php?pid=<?php echo $record['ProctorId'];?>">
	<input type="submit" name="Proc" value="Change" />
    </form> </td>
    
    <td bgcolor="#FFFFFF">&nbsp; </td>
    </tr>


 
<? 
}   



  

//}
?>
</table>
<br />
<br />
<table width= "100%" border="0">
<tr  width= "100%" class="menu">
	<td width= "100%" align= "center"><span><strong>Islamic Online University Exam Centers Registration Portal</strong></span></td></tr>
 </table>
<?
 /* }






 else {
 header("location: loginerror.php");
 //exit;
             
   }