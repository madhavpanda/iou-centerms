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
<link rel="stylesheet" href="CenterCMS.css" />

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
.td_data td  {
	color:#FFFFFF;
	text-decoration:none;
	cursor:pointer;
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
<title>Islamic Online University Exam Centers Registration</title>
<table width= "100%" border="0">
<tr  width= "100%"  class="menu">
	<td width= "87%" align= "center">EXAM CENTERS REGISTRATION PORTAL <td width="13%"><a href="examcenternotice.php"><span><strong>Notice Board</strong></span></a></td></td>
      
</tr>
</table>




      <table id="layout-table" summary="layout" width="100%">

<tr>
          <td id="middle-column" width="100%" valign="top"><span id="maincontent"></span>
            
         <p><?
         if (empty($USER->lastname)){$x="Student";}
         else {$x=$USER->lastname.' '.$USER->firstname;}
         echo "Welcome ".$x.","?> </p>
    <p>  <?
         if (empty($USER->lastname)){$x="Student";}
         else {$x=$USER->lastname.' '.$USER->firstname;}
         echo "Dear ".$x.","?> <br><br>
    To find an exam center in your area please choose your country from the list below. A list of approved centers in your country will be displayed. If you find a <br>
    suitable center click on 'choose' for that center. You will then receive an email confirming your registration, with us, for that center.<br>

    If there is no center in your area you will need to find a suitable center ( as per the requirements found here ).<br><br>
     
     Please read the requirements carefully and follow the link from this page to suggest your center.<br> 
     You will be informed when your suggested center has, inshaAllah, been approved. you will then need to re visit this page to 'choose' your center.<br><br>

    Important Points:<br>
    1. It is every students responsibility to contact the center they wish to use to agree on dates and times for the exams etc. ( you may find some centers only operate<br> at 
    the weekends or can only accommodate a certain number of students at any one time)<br><br>

    2. When suggesting a new center it is vital that all information is fully and correctly filled in, failure to do so could result in the center being rejected outright.<br><br>

    3. It takes quite some time to approve and confirm a new center and therefore requests for new centers will not be accepted after 1st of December, and you may then <br>
    be required to travel to the nearest IOU approved center. We urge you therefore to submit your requests as soon as possible and certainly by the 1st of December<br><br>

    4. Those students who have already been with us for a semester or 2 are also required to choose their center so that their names are recorded in this database.<br><br>
    
    5.We have set up a Notice Board to provide students with news and updates on the centers. Those who do not have an approved center assigned to them should check the board<br> 
    regularly.The Notice Board may be accessed by clicking on the link in the upper right corner of this page.<br><br></p>
    
    Please Choose the country in which you wish to take your exams<br><br>
        </td>
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
?>
<table width= "100%" border="0">
<tr  width= "100%" class="menu">
	<td width= "100%" align= "center"><span><strong>Islamic Online University Exam Centers Registration Portal</strong></span></td></tr>
 </table>