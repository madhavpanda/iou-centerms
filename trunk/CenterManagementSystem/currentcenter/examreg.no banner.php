<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<? #session_start();
  
   require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
   //require_once('/home/eomanico/public_html/ecampus/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   


   //unset($_SESSION['sch_page_name']);
    include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password );
   
   ?>
<link rel="stylesheet" href="CenterCMS.css" /><link rel="shortcut icon" href="http://bais.islamiconlineuniversity.com/bais/theme/ingenuous/favicon.ico" /><style type="text/css">

.class6{
	text-align: justify;
	font-size:15px;
	font-family: times new roman;      
}
ul {
list-style-image:url('http://bais.islamiconlineuniversity.com/bais/center/buttons/sqpurple.gif');
}
.class2{
font-size:15px;
font-family: times new roman;      
}
#page_content{
width: 720px;
margin-left: auto;
margin-right: auto;
padding: 35px; 
border-right: 15px solid #B7CEEC;
border-left: 15px solid #B7CEEC;
}
.menu1{
	color :blue;
	font-size: 16px;
}
.menu2{
color:blue;

}
.menu3{
text-align:right;
}
#noticelink{
color : ;
}

<!--body {
	font-family: Arial;
	font-size: 10pt;
	line-height: normal;
}table {
	font-family: Arial;
	font-size: 10pt;
	line-height: normal;
}.verdana_10px {	font-family: Verdana;	font-size: 11px;	line-height: normal;}.td_data td {	padding: 4px}.td_data td  {	color:#FFFFFF;	text-decoration:none;	cursor:pointer;}a:link {
	color:#0033FF;
	text-decoration: none;
}a:visited {
	color:#0033FF;
	text-decoration: none;
}a:hover {
	color:#363687;
	text-decoration: underline;
}a:active {
	color:#0033FF;
	text-decoration: none;
	font-size: 10px;
}-->
</style><title>Islamic Online University Exam Centers Registration</title>
<div id="page_content">


<table width= "100%" border="0"><tr  width= "100%"  class="menu1">	<td width= "83%" align= "center"><strong>
  <h2 class="menu2">&nbsp;</h2>
  <h2 class="menu2"><img src='http://bais.islamiconlineuniversity.com/bais/center/buttons/logo5m.jpg' width="71" height="70" /> EXAM CENTERS REGISTRATION PORTAL </h2>
</strong>      
<td width="16%"><a id=noticelink href="examcenternotice.php" class="menu3"><strong>Notice Board</strong></a></td>
<td width="1%"></td>      </tr></table>     

     
      


 <table id="layout-table" summary="layout" width="100%"><tr>          <td id="middle-column" width="100%" valign="top"><span id="maincontent"></span>                     <p class="class2">&nbsp;</p>
      <p class="class2"><?
         if (empty($USER->lastname)){$x="Student";}
         else {$x=$USER->lastname.' '.$USER->firstname;}
         echo "Welcome ".$x.","?> </p>
    <p class="class2" >  <?
         if (empty($USER->lastname)){$x="Student";}
         else {$x=$USER->lastname.' '.$USER->firstname;}
         echo "Dear ".$x.","?> </p>
      <p class="class6">To find an exam center in your area </p>
          <ul>   
          <li>      Please choose your country from the list below. A list of approved centers in your country will be displayed.</li>
          <li>   If you find a suitable center click on 'choose' for that center. You will then receive an email confirming your registration, with us, for that center.</li>
          <li>   contact the center you wish to use to check availability and to agree times ect ( there is no need to inform us of that) we cannot arrange that on your behalf so it is vital that you contact the center yourself. </li>
          </ul>
       <p class="class6">  If there is no center suitable for you </p>
                <ul>
         <li> Find a suitable center (check the criteria at this link - <a href="http://bais.islamiconlineuniversity.com/bais/mod/resource/view.php?id=575">http://bais.islamiconlineuniversity.com/bais/mod/resource/view.php?id=575</a> ) contact the center to establish if they are able and willing to act as an exam center. Do not send us a centers details before you have established if they are willing to act as a center. </li>
         <li>    Follow the link from this page to suggest your center. You will be informed when your suggested center has, inshaAllah, been approved. you will then need to re visit this page to 'choose' your center.</li>
         <li>   Do not email your suggested exam center's details to the exam center management , your suggested center will not be contacted via such emails.</li>
        </ul>
              
  <p class="class6">        Important Points:</p>
  <ul>
         <li>  It is every students responsibility to contact the center they wish to use to agree on dates and times for the exams etc. (you may find some centers only operate at the weekends or can only accommodate a certain number of students at any one time).</li>
         <li>  When suggesting a new center it is vital that all information is fully and correctly filled in, failure to do so could result in the center being rejected outright.</li>
        <li>   It takes quite some time to approve and confirm a new center and therefore requests for new centers will not be accepted after 1st of December, and you may then be required to travel to the nearest IOU approved center. We urge you therefore to submit your requests as soon as possible and certainly by the 1st of December (although in the past we have been lenient about this, due to the large numbers of students we now have the 1st December deadline will be strictly upheld and centers suggested after that date will not be accepted).</li>
       <li>    Those students who have already been with us for a semester or 2 are also required to choose their center so that their names are recorded in this database.</li>
        <li>  We have set up a Notice Board to provide students with news and updates on the centers. Those who do not have an approved center assigned to them should check the board regularly.The Notice Board may be accessed by clicking on the link in the upper right corner of this page.</li>
      </ul>      <p class="class2">Please Choose the country in which you wish to take your exams :</p></p>
</td>        
</tr>
  <tr>
    <td id="middle-column2" valign="top">&nbsp;</td>
  </tr>
</table>
<?
//echo "Please Choose the country in which you wish to take your exams".'<br />';
   //echo $mysql_connect_id;
   
echo '<form id="form1" method="POST" action="centerlist.php">';
 $query="SELECT CountryId,CountryCode,CountryName FROM Countries";

/* You can add order by clause to the sql statement if the names are to be displayed in alphabetical order */

$result = mysql_query ($query);
echo "<select name=country value=''>Country Name</option>";
// printing the list box select command

while($nt=mysql_fetch_array($result)){//Array or records stored in $nt
echo "<option value=$nt[CountryId]>$nt[CountryName]</option>";
/* Option values are added by looping through the array */
}
  echo '<input type="submit" name="submit" id="button" value="Choose" />';
echo "</select>";// Closing of list box   

echo '</form>';
 
/*
if ( $USER->id ) {
	
  if (isset ($_POST['country'])) {
  $country_id=$_POST['country'];
  echo $country_id;
   }
   }*/
?><table width= "100%" border="0"><tr  width= "100%" class="menu1">	<td width= "100%" align= "center"><p>&nbsp;</p>
      <p>&nbsp;</p>
      <p><span><strong>Islamic Online University Exam Centers Registration Portal</strong></span></p></td></tr> </table>
</div>
