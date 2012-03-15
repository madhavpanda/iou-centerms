<script src="http://code.jquery.com/jquery-1.6.4.min.js" type="text/javascript"></script>
<script>
function getStatus(str){
//document.getElementById("txtHint").innerHTML=val;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("SuggestPage").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","sugdisplay.php?q="+str,true);
xmlhttp.send();
}
</script>

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
   
   //check for suggest page display status
   $checkpage = mysql_extract_records_where( "", "CenterSuggestPage", "id=1 ", "status" );
//echo $check;
$status=$checkpage[1]['status']; 

if ($status==1)// suggest page offline 
{
 $inputvalue= "Suggest page offline";
}
   else{// suggest page online
	   $inputvalue= "Suggest page online";
   
   }
  
   
   $sortFieldName = $_GET['sort'] ? $_GET['sort'] : "CountryId";
	$sortFieldType = $_GET['sorttype'] ? $_GET['sorttype'] : "asc";
   $mysql_connect_id = mysql_start( $mysql_server, $moodle_db, $mysql_username, $mysql_password );
   $FORM['return_query_mark'] = ( $FORM['return_query'] ) ? "?" . preg_replace( "/^\\?+/", "", $FORM['return_query'] ) : "";

 if ($FORM['status'] ) {
  
      $field_name = mysql_real_escape_string( $_GET['status']);
	  if($field_name==1){$activeno="=1";}
	  else{$activeno="=0";}
     $where_clause[] = " Active $activeno";
 }
 
 if ($FORM['contry'] ) { 
	 $contry=$_GET['contry'];
	 
	 $where_clause[] = " CountryId = '".$contry."'";
	   
	  
 }
 
 
 
 //$where_clause[] = " Active = $field_name ";
   //$where_clause[]= " payment_amount > 0";
   $select_addons = array( "WHERE" => join( " AND ", $where_clause ), "ORDER BY" => $sortFieldName." ".$sortFieldType, "UNIQUE" => "Y" );
   $fields_to_extract = "CenterId,CenterName,CityId,Address1,Website,StatusId,PrimaryPhone,Email,Active,CountryId,CityName,EnteredById" . $extra_fields;
   $mysql_connection_id = ""; 
   $centers="ExamCenters";
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
<tr  width= "100%"  class="menu">
	<td width= "80%" align= "center"><span><strong>List of All Exam Centers</strong></span></td><td width= "20%" align= "right"><strong><div id="SuggestPage"><input type="submit" name="suggest" value="<?= $inputvalue ; ?>" onClick='getStatus(this.value)'></div></strong>
    
    <div id="txtHint"> </div></td>
    
      
</tr>
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
<table>
<tr class="menu">
     
     <td width="11%"><span><strong>Country</strong></span></td>
    <td  width="9%"><span><strong>City</strong></span></td>
    <td  width="12%"><span><strong>Center Name</strong></span></td>
    <td  width="12%"><span><strong>Address</strong></span></td>
    <td  width="8%"><span><strong>Center email and Website	</strong></span></td>
    <td  width="12%"><span><strong>Phone no</strong></span></td>
    
    <!-- <td  width="5%"><span><strong>Student Capacity</strong></span></td>-->
   <td  width="10%"><span><strong>Status</strong></span></td>
    <td  width="5%"><span><strong>Proctor</strong></span></td>
    <td width="3%" align="center"><span><strong>Center Id</strong></span></td>
  </tr>	
<?php
	$bg_color = "#F0F0F0";
	foreach ( $search_results as $record ) {
  
   //print_r($record);
		$bg_color = ( !$bg_color ) ? "#F0F0F0" : "";
      
      $countryid=$record['CountryId'];
      $country_data = mysql_extract_records_where( "", "Countries", "CountryId=$countryid" , "CountryName" );
	  
	   foreach ( $country_data as $c_record ) {
	  $country=  $c_record['CountryName'];
	  }
	
 
$city=$record['CityName'];	
 $active=$record['Active'];
 if ($active==0){
  // $status="<a href='statusrev.php?who=$record[EnteredById]' onclick='return popitup(statusrev.php?who=$record[EnteredById])'>Unapproved</a>";
   
  $status="<a href='statusrev.php?who=$record[EnteredById]&&centid=$record[CenterId]' target='_blank'> Unapproved </a>";
 }
 else{$status="Approved";}
 
 $centername=$record['CenterName'];
 //echo $centername;
 $centeremail=$record['Email'];
// echo $centeremail;
 $centerphone=$record['PrimaryPhone'];
// echo $centerphone;
 $centerweb=$record['Website'];
 //echo $centerweb;
 $capacity=$record['Capacity'];
// echo $capacity
 $enteredby=$record['EnteredById'];
  $address=$record['Address1'];
 $centerid=$record['CenterId']; 
//echo $bg_color; 
?>
 <tr bgcolor="#C9C9C9" >
     
    
    <td> <?php echo $country; //print $country ?> </td>
    <td> <?php print $city ?> </td> 
    <td> <?php print $centername ?> </td>
    <td> <?php print $address ?> </td>
    <td> <?php print $centeremail .'<br />'.$centerweb ?> </td>
    <td> <?php print $centerphone ?> </td>
    
    <!--<td> <?php print $capacity ?> </td>-->
    <td> <?php print $status ?> </td>
   
    <td> <a href="proctordet.php?id=<?php echo $record['CenterId']; ?>&&name=<?php echo $record['CenterName']; ?>" target="_blank"> Proctor</a></td>
     <td> <?php print $centerid ?> </td>
    

    </tr>

<? }   

//}
  //}





 //else {
 //echo "No way u aint logged in yet";
	 //$arrFileInfo = explode("/",$_SERVER['SCRIPT_NAME']);
	//$_SESSION['sch_page_name'] = "/".$arrFileInfo[count($arrFileInfo)-1]

 //  }
  
 ?>
  <tr><td colspan="14" align="center" height="40" class="verdana_10px">
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
<option value="1">Approved</option>     
<option value="2">Unapproved</option>

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
 <option value="<? echo $nt['CountryId']; ?>"> <? echo $nt['CountryName']; ?> </option>
<? 
}
?>
</select> 
        <input type="submit" value="Go"></td>
    </form></tr>
 </table> 
 <table width= "100%" border="0">
<tr  width= "100%" class="menu">
	<td width= "100%" align= "center"><span><strong>Islamic Online University Exam Centers Registration Portal</strong></span></td
 ></table>
  <script>
	
		
function popitup(url) {
	newwindow=window.open(url,'name','height = 380, width = 550,left=350,top=200');
	if (window.focus) {newwindow.focus()}
	return false;
}

</script>