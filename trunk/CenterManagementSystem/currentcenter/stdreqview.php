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
   $sortFieldName = $_GET['sort'] ? $_GET['sort'] : "CenterName";
	$sortFieldType = $_GET['sorttype'] ? $_GET['sorttype'] : "asc";
   $mysql_connect_id = mysql_start( $mysql_server, $moodle_db, $mysql_username, $mysql_password );
   $FORM['return_query_mark'] = ( $FORM['return_query'] ) ? "?" . preg_replace( "/^\\?+/", "", $FORM['return_query'] ) : "";

 if ($FORM['status'] ) {
  
      $field_name = mysql_real_escape_string( $_GET['status']);
	  if($field_name==1){$activeno="=1";}
	  else{$activeno="=0";}
     $where_clause[] = " status $activeno";
 }
 
 if ($FORM['contry'] ) { 
	 $contry=$_GET['contry'];
	 
	 $where_clause[] = " Country = '".$contry."'";
	   
	  
 }
 
 
 
 //$where_clause[] = " Active = $field_name ";
   //$where_clause[]= " payment_amount > 0";
   $select_addons = array( "WHERE" => join( " AND ", $where_clause ), "ORDER BY" => $sortFieldName." ".$sortFieldType, "UNIQUE" => "Y" );
   $fields_to_extract = "CentrereqId,FirstName,LastName,Email,CenterName,City,Country,status" . $extra_fields;
   $mysql_connection_id = ""; 
   $centers="CentreRequest";
   $table_to_query = $centers;

   $current_page_query_string_for_link = preg_replace( "/^%3F/", "", htmlentities(urlencode($_SERVER['QUERY_STRING'])) );

   

   #$sql_debug = 1;
   $records_per_page = 20;
   include( "php_lib3/query_results_begin.php" );
   
   
   
   
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
<?

if($_GET)
{	
	///$str = implode("&",$_GET);
	///echo $str;
	//$str = implode("&",array_keys($_GET));
	//echo $str;
	$arrKeyArray = $_GET;
	foreach($arrKeyArray as $key => $value)
	{
		//$queryString .= $key."=".$value."&";
	}

	$queryString = urldecode($_SERVER['QUERY_STRING'])."&";
		//echo strstr($queryString,"start=");
	$queryString =  str_replace(strstr($queryString,"start="), "", $queryString);;
	$queryString =  str_replace(strstr($queryString,"sorttype="), "", $queryString);;
	$queryString =  str_replace(strstr($queryString,"sort="), "", $queryString);;
	//echo $queryString;
}
$queryString = urldecode($_SERVER['QUERY_STRING'])."&";
//SELECT id, GROUP_CONCAT(string SEPARATOR ' ') FROM table GROUP BY id;
$sortFieldTypeOppId = $_GET['sort'] == "CountryId"  ?  ($_GET['sorttype'] != "asc" ? "asc" : "desc" ) : "asc" ;
$sortFieldTypeOppUsername = $_GET['sort'] == "notes"  ?  ($_GET['sorttype'] != "asc" ? "asc" : "desc" ) : "asc" ;
//$sortFieldTypeOppGender = $_GET['sort'] == "gender"  ?  ($_GET['sorttype'] != "asc" ? "asc" : "desc" ) : "asc" ;
$sortFieldTypeOppFirstname = $_GET['sort'] == "firstname" ?  ($_GET['sorttype'] != "asc" ? "asc" : "desc" ) : "asc" ;
$sortFieldTypeOppEmail = $_GET['sort'] == "email"  ?  ($_GET['sorttype'] != "asc" ? "asc" : "desc" ) : "asc" ;
$sortFieldTypeOpptitle = $_GET['sort'] == "title"  ?  ($_GET['sorttype'] != "asc" ? "asc" : "desc" ) : "asc" ;
$sortFieldTypeOpppayment = $_GET['sort'] == "payment_date"  ?  ($_GET['sorttype'] != "asc" ? "asc" : "desc" ) : "asc" ;
?>
<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
<tr  class="menu">
     <td width="4%" bgcolor="#FFFFFF">&nbsp;</td>
     <td width="11%"><span><strong>Name</strong></span></td>
    <td  width="10%"><span><strong>Email</strong></span></td>
    <td  width="12%"><span><strong>Center Name</strong></span></td>
    <td  width="12%"><span><strong>City	</strong></span></td>
    <td  width="12%"><span><strong>Country</strong></span></td>
    <td  width="12%"><span><strong>Confirm</strong></span></td>
     <td  width="12%"><span><strong>Proctor Info</strong></span></td>
    <td  width="12%"><span><strong>Status</strong></span></td>
    
    <td width="6%" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>	
<?
$bg_color = "#F0F0F0";
	foreach ( $search_results as $record ) {
  

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
    
    
    <td><?php $statcheck= "select status from CentreRequest where email= '".$record['Email']."' " ;
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
 

  <?php
   }
?>
 
  <tr><td colspan="14" align="center" height="40" >
 Showing <?php print $starting ?> - <?php print $ending ?> of <?php print $total_records ?> [Page <?php print $current_page ?> of <?php print $total_pages ?>]<br>
      <?php include( "php_lib3/query_results_end.php" ); ?></td>
  </tr>
  <?
	$queryStringDownload = urldecode($_SERVER['QUERY_STRING'])."&";
	$queryStringDownload =  str_replace(strstr($queryStringDownload,"&start="), "", $queryStringDownload);;
  ?>
  <tr>
  <form>
      <td colspan="14" style="padding-left: 10px">Quick Filters
       
        <br>
       <select name="status">
          <option value="">Status</option>
<option value="1">Notified</option>     
<option value="2">Unnotified</option>

        </select> 
      AND
        
          <?php 
         $query="SELECT CountryName,CountryId  FROM Countries ORDER BY CountryId asc";



$result = mysql_query ($query);?>

<select name='contry' >
<option value="">Country</option>

<?

while($nt=mysql_fetch_array($result)){

?>
 <option value="<? echo $nt['CountryName']; ?>"> <? echo $nt['CountryName']; ?> </option>
<? 
}
?>
</select> 
        <input type="submit" value="Go"></td>
    </form></tr>
 
</table>

<table width= "100%" border="0">

<tr  width= "100%" class="menu">
   <td width= "80%" align= "center" ><span><strong>Islamic Online University Exam Centers Registration Portal</strong></span><td width="20%"><span ><strong><a href="download-center-request.php" style="color:#CCC">Excel Download</a></strong></span></td></strong></span></td></tr>
	
 </table>