<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 
.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<? #session_start();
  
  require_once('/var/www/bais.islamiconlineuniversity.com/bais/config.php');
   require_once($CFG->dirroot .'/course/lib.php');
   require_once($CFG->dirroot .'/lib/blocklib.php');
   


   //unset($_SESSION['sch_page_name']);
    include( "centerconn.php" );
	include( "php_lib3/misc.php" );
   include( "php_lib3/mysql.php" );
   
   $mysql_connect_id = mysql_start( $mysql_server, $center_db, $mysql_username, $mysql_password );
   
   ?>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/ddsmoothmenu.css" />
<!--[if lte IE 7]>
<style type="text/css">
html .ddsmoothmenu{height: 1%;} /*Holly Hack for IE7 and below*/
</style>
<![endif]-->
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/ddsmoothmenu.js"></script>
<link rel="shortcut icon" href="favicon.ico">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Islamic Online University</title>
<? include_once("templates/analytics.php")?>
<script src="js/AC_RunActiveContent.js" type="text/javascript"></script>


</head>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" valign="top"><table width="1014" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="3%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td><? include_once("templates/header.php")?></td>
                    </tr>
                  </table>
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="3%" background="images/left-bg.png">&nbsp;</td>
                      <td width="97%" bgcolor="#FFFFFF"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td><? include_once("templates/menu.php");?></td>
                          </tr>
                          <tr>
                            <td height="35" align="left" bgcolor="#000000" class="toplink" style="border-top: #ffffff 1px solid";>&nbsp;&nbsp;&nbsp; BAIS Degree Page &gt;&gt;  Exam Centers    &gt;&gt;  Approved Exam Centers   </td>
                          </tr>
                          <tr>
                            <td height="24">&nbsp;</td>
                          </tr>
                          <tr>
                            <td><table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                  <td width="100%"><table width="100%" cellpadding="0" cellspacing="0" >
                                      <tr>
                                        <td align="left"><span class="headerblue">IOU Approved Exam Centers</span></td>
                                      </tr>
                                      <tr>
                                        <td>&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td class="text3" align="justify"><p>Below is the list of IOU Approved Centers where exams have been conducted in the past. If one of them is suitable for you then you may give your exams there. However we are unable to guarantee that all the centers below will be able to conduct exams in future. Students would need to confirm with them each semester that they would be conducting the exams that particular semester. If some center is unable to conduct the exams, students would need to identify an alternative center.</p>
                                          <p>Students who fail to come up with a center in their area will be obliged to travel to the nearest IOU approved Center in or outside of their city, state or country.</p></td>
                                      </tr>
                                      <tr>
                                        <td><center><table width="100%" style=" border:1px #000 solid; " border="0">
  <tr style="color:#FFF" bgcolor="#003372"> 
    <td ><strong>Country</strong></td>
    <td ><strong>City</strong></td>
    <td ><strong>Address</strong></td>
    <td ><strong>Contact Infromation</strong></td>
  </tr>
  <?php
  $flag=1; // for dynamically changing the color of tr
$allcenter= "Select * from ExamCenters where Active=1 order by CountryId  ";

$getall = mysql_query($allcenter);
while($record=mysql_fetch_array($getall)){
	$countryid=$record['CountryId'];
	
	$countryqry= "Select * from Countries where CountryId=$countryid";
	
	$getcountry=mysql_query($countryqry);
	while($countryrec=mysql_fetch_array($getcountry)){
		$country=$countryrec['CountryName'];
		
	}
	$city=$record['CityName'];
	$centername=$record['CenterName'];
	$centeremail=$record['Email'];
	$centerphone=$record['PrimaryPhone'];
	$centerweb=$record['Website'];
	$address=$record['Address1'];
    if ($flag==0)
	 {
		 $flag=1;
	 }
	 else
	 {
		 $flag=0;
	 }
	 

	?>
  <tr  <?php if ($flag==0) {echo 'bgcolor="#FFF"';}else { echo 'bgcolor="#d8d8d8"';}?>>
    <td><?php echo $country; ?> </td>
    <td><?php print $city ?> </td>
    <td><?php print $centername .'<br/>'. $address ?></td>
    <td><u>Email:</u> <?php print $centeremail .'<br /><u>Website:</u> '.$centerweb .'<br /><u>Phone:</u> '.$centerphone ?></td>
  </tr>
  <?php } ?>
                                        </table></center>
</td>
                                      </tr>
                                      <tr>
                                        <td class="text3" valign="top">
                                        
                                        
                                        </td>
                                      </tr>
                                    </table></td>
                                  <td width="2%"></td>
                                </tr>
                              </table></td>
                          </tr>
                          <tr>
                            <td>&nbsp;</td>
                          </tr>
                          <tr>
                            <td><? include_once("templates/footer1.php");?></td>
                          </tr>
                          <tr>
                            <td>&nbsp;</td>
                          </tr>
                          <tr>
                            <td align="center"><? include_once("templates/footer2.php");?></td>
                          </tr>
                        </table></td>
                      <td background="images/right-bg.png">&nbsp;</td>
                    </tr>
                    <tr>
                      <td><img src="images/left-bottom-curve.png" width="34" height="32" /></td>
                      <td background="images/bottom-bg.png">&nbsp;</td>
                      <td><img src="images/right-bottom-curve.png" width="36" height="32" /></td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
</body>
</html>