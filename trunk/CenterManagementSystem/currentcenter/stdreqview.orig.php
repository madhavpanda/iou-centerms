<? #session_start();
  include("accesscontrol.php");
   require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   


   //unset($_SESSION['sch_page_name']);
    include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password );
   
   ?>
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
<table width= "100%" border="0">
<tr  width= "100%" class="menu">
	<td width= "100%" align= "center"><span><strong>Students Exam Center Request</strong></span></td>
      
</tr>
</table>

<? include_once("linkmenu.php"); ?>
</table>
<? include_once("linkmenu.php");?>
<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
<tr  class="menu">
     <td width="4%" bgcolor="#FFFFFF">&nbsp;</td>
     <td width="11%"><span><strong>Name</strong></span></td>
    <td  width="10%" style="color:#FFFFFF"><strong>Email</strong></td>
    <td  width="12%"><span><strong>Center Name</strong></span></td>
    <td  width="12%"><span><strong>City	</strong></span></td>
    <td  width="12%"><span><strong>Country</strong></span></td>
    <td  width="12%"><span><strong>Confirm</strong></span></td>
     <td  width="12%"><span><strong>Proctor Info</strong></span></td>
    <td  width="12%"><span><strong>Status</strong></span></td>
    
    <td width="6%" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>	
<?
$max = 40;
			  $_GET[page_no] = $_GET[page_no] ? $_GET[page_no] : 1;
			  $start = ($_GET[page_no] -1) * $max;


$query="select * from CentreRequest order by CenterName";
$result=mysql_query($query);

 while ( $record  = mysql_fetch_array($result, 1)){
 $firtsname=$record['FirstName'];
  $lastname=$record['LastName'];
 $centername=$record['CenterName'];
 $city=$record['City'];
 $country=$record['Country'];
 
 
 
?>

  <tr bgcolor="#C9C9C9">
 
     <td bgcolor="#FFF">&nbsp;</td>
  <td><?php print $record['FirstName'] . " " . $record['LastName'] ?></td>
    
    
    
    
    <td><?php print  $record['Email']  ?></td>
	<td><?php print $centername ?></td> 
    <td><?php print $city ?></td>
    <td><?php print $country ?></td>
    <td> <form id="reg" method="POST" action="centerconmail.php?mail=<?php echo $record['Email']; ?>&&fname=<?php echo $record['FirstName']; ?>">
	<input type="submit" name="enroll" value="Notify" />
    </form>
     </td>
     
     <td> <form id="reg" method="POST" action="proinfomail.php?mail=<?php echo $record['Email'];?>&&cent=<?php echo $centername; ?>">
	<input type="submit" name="SendProc" value="Send Proctor Info" />
    </form>
       
     </td>
    
    
    <td><?php $statcheck= "select * from CentreRequest where email= '".$record['Email']."' " ;
       $checkres=mysql_query($statcheck);
       while ( $rec = mysql_fetch_array($checkres, 1)){
 $status=$record['status'];}
 //echo $status;
 if($status != 0)
 { echo "Notified"; }
 else
 {echo "Yet to be Notified";}
 
 
    
    
     ?></td>
    <td bgcolor="#FFF">&nbsp;</td>
    </tr>
 
<!--<table width="100%">-->

<!--<td  bgcolor="#808080" width="1" height="1"></td>-->

  <!--<td  width="100%" ></td>-->  
  
 <!--</tr>-->

 <!--</table>-->
  <?php
   }
?>
  <?php
 
  
	$queryStringDownload = urldecode($_SERVER['QUERY_STRING'])."&";
	//echo $queryStringDownload;
	$queryStringDownload =  str_replace(strstr($queryStringDownload,"&start="), "", $queryStringDownload);;
  ?>
  <tr>
    <td colspan="14" align="center" height="15" class="verdana_10px">
    
    <?

if($_SERVER['QUERY_STRING'])
	{
		$querypress=explode("&",$_SERVER['QUERY_STRING']);
		if(count($querypress) >= 1)
		{
			for($m=0;$m<(count($querypress));$m++)
			{
				list($qryparam,$valparam) = explode("=",$querypress[$m]);
				if($qryparam != "page_no")
				{
					$arrpress .= $querypress[$m]."&";
				}
			}
		}
	}
	?>
	<? echo showPaginationLink($_GET[page_no],"serial-check.php?".$arrpress."&page_no=",$length,$max)?></td></tr>
 
</table>

<p>&nbsp;</p>
<?
function showPaginationLink($current_page, $link, $length, $max)
{
    $page = ceil($length / $max);
	if($page > 1)
	{					
		for($i=1;$i<=$page;$i++)
		{
			$str .=($current_page == $i) ? '<strong>'.$i.'</strong>' : '<a href="#" style="color:#999999; font-weight: bold; text-decoration: none; "onclick="javscript:location.replace(\''.$link.$i.'\')"> '.$i.' </a>';
		}
		$new_str =  "<strong>Page</strong> - " .$str;
	}
  return $new_str;
}

?>
<table width= "100%" border="0">

<tr  width= "100%" class="menu">
   <td width= "80%" align= "center" ><span><strong>Islamic Online University Exam Centers Registration Portal</strong></span><td width="20%"><span><strong><a href="download-center-request.php">Excel Download</a></strong></span></td></strong></span></td></tr>
	
 </table>