<?php
   #session_start();
   include( "../register/cfg.php" );
   include( "../register/php_lib3/misc.php" );
   include( "../register/php_lib3/mysql.php" );
//$database_name = "eomanico_mdle1";
 $database_name = "bais_baismood";
   //$field_id = 5;
   $mysql_connect_id = mysql_start( $mysql_server, $database_name, $mysql_username, $mysql_password );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="http://bais.islamiconlineuniversity.com/bais/theme/standard/styles.php" />
<link rel="stylesheet" type="text/css" href="http://bais.islamiconlineuniversity.com/bais/theme/ingenuous/styles.php" />
<meta name="description" content="" />
<!--[if IE 7]>
		    <link rel="stylesheet" type="text/css" href="http://bais.islamiconlineuniversity.com/bais/theme/standard/styles_ie7.css" />
		<![endif]-->
<!--[if IE 6]>
		    <link rel="stylesheet" type="text/css" href="http://bais.islamiconlineuniversity.com/bais/theme/standard/styles_ie6.css" />
		<![endif]-->
<meta name="keywords" content="moodle, Islamic Online University - BAIS " />
<title>Islamic Online University - Withdrawal Failure grades dump </title>
<link rel="shortcut icon" href="http://bais.islamiconlineuniversity.com/bais/theme/ingenuous/favicon.ico" />
<!--<style type="text/css">/*<![CDATA[*/ body{behavior:url(http://bais.islamiconlineuniversity.com/bais/lib/csshover.htc);} /*]]>*/</style>-->
<style type="text/css">
<!--
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
.c {
	font-family: Arial Black, Gadget, sans-serif;
}
-->
</style>
<link rel="stylesheet" type="text/css" media="all" href="css/winxp.css" title="winxp" />
<script type='text/javascript' src='js/zapatec.js'></script>
<script type="text/javascript" src="js/calendar.js"></script>
<!-- import the language module -->
<script type="text/javascript" src="js/calendar-en.js"></script>
<!-- other languages might be available in the lang directory; please check
your distribution archive. -->
<!-- import the calendar setup script -->
<script type="text/javascript">
function getSemID(str)
{
document.getElementById("txtHint").innerHTML='<img src="images/loading.gif">';
if (str=="")
  {
  document.getElementById("txtHint").innerHTML="";
  return;
  }  
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
    document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","showsubjects.php?q="+str,true);
xmlhttp.send();
}

</script>
</head>
<body class="course course-1 notloggedin dir-ltr lang-en_utf8" id="site-index" width="100%">
<div id="page" width="100%">
<div id="header-home" class=" clearfix" width="100%">
  <div class="wrapper clearfix"> <img class="logo" src="http://bais.islamiconlineuniversity.com/bais/theme/ingenuous/pix/logobanner.png"/>
    <div class="headermenu">
      <div class="logininfo"></div>
    </div>
  </div>
  <div id="menubox" class="wrapper clearfix">
    <ul id="menu">
      <li><a href="http://bais.islamiconlineuniversity.com/bais" title="Home">Home</a></li>
      <li><a href="http://bais.islamiconlineuniversity.com/bais/course/index.php" title="Courses">Courses</a></li>
      <li><a href="http://bais.islamiconlineuniversity.com/bais/calendar/view.php?view=month" title="Calendar">Calendar</a></li>
    </ul>
  </div>
</div>
<div class="background" width="100%">
  <!-- END OF HEADER -->
  <div id="content" width="100%">
    <div class="wrapper" width="100%">
      <table id="layout-table" summary="layout" width="100%">
        <tr>
            <td id="middle-column" width="100%" valign="top"><p  align="center" ><strong> Withdrawal Failure Grades Dump Utility Page</strong></p>
            
                    
            <p>NOTE:The csv to be uploaded must have the course id in the first column and student id  in the second column of the csv file.If you are using the excel file from the course drop page view, remove the headers from first line of the sheet and also remove the all other columns except the first two columns which comprises of the course id and student id.Then upload.</p>
        
            <div>
              <?php

   if ( $FORM['submit'] ) 
   {
   
   
	//$user_id=$_POST['userid'];
	$error = false;
	$date= date("Y-m-d");
	//$user_id="1";
	$filename=$_FILES["file"]["name"];
	
	$sem_id=$_POST['sem_list'];
	//$subj_id=$_POST['subj_list'];
	$type=$_POST['type'];
	$year=$_POST['academic_year'];
	$date=$_POST['date8a'];
	$score= 0.00;
	$wfresult= "WF";
	//$times_repeat=$_POST['times_repeat'];
	//echo "File NAme = ".$filename."<br>";
	/*echo "Semester = ".$sem_id."<br>";
	echo "Subject = ".$subj_id."<br>";
	echo "Session = ".$session."<br>";
	echo "Academic Year = ".$academic_yr."<br>";
	echo "Lecture Date = ".$lect_date."<br>";
	echo "Date =".$date."<br>";*/

	if(($sem_id==' ') || ($type==' ') || ($year==' ') || ($date==' ')) 
	{ 
	$error = true;
	echo "<script>alert('Please Fill forms properly.Did you select a Semester,Date,Type and Year? You must have missed one :)')</script>"; 
	}
	$i = strrpos($filename,".");
    if (!$i) { echo "Ooops File without Extension"; }
    $l = strlen($filename) - $i;
    $ext = substr($filename,$i+1,$l);
	//echo "Extension is = ".$ext."<br>";
	if ($ext!= "csv")
	{
		$error = true;
		echo "<script>alert('Upload Only CSV file')</script>";
	}
	else
	{
		
		move_uploaded_file($_FILES["file"]["tmp_name"],"upload/" . $_FILES["file"]["name"]) or die("Error 2 occured!!");
	}


if($error == false)
{
	
	
//check if the duplicate data existed in the system
	
$result = mysql_query("SELECT * FROM `mdl_i_usercourse_hst` where 
`course_id` ='".$subj_id."' and 
 year  = '".$year."'  and 
`type`	= '".$type."'  and 
`sem_id`= '".$sem_id."'"); 
	$row = mysql_fetch_array($result);
	if ($row){ echo "These sets of records likely exists in the database please check and confirm";}
	
	elseif(!$row)
	{
		//read the data into the db from the csv file
	$fp = fopen("upload/" . $_FILES["file"]["name"],'r') or die("can't open file");
	
	
	// $handle = fopen("$filename", "r");
	while (($data = fgetcsv($fp, 1000, ",")) !== FALSE)

     {
//read in data from the csv file from the 1st and 2nd column
   $subj_id= $data[0];
    $userid = $data[1];
	//$score = $data[7];
/*	if ($score == '-'){ $score=0;}
	
	$result = ($score >= 60.00) ? "Pass" : "Fail";
	*/
	//check for students who are having WF in the same exam the second time 
	 $sql_check= "select * from mdl_i_usercourse_hst where user_id= '".$userid."' and `course_id` = '".$subj_id."'  and `result` = '".$wfresult."' ";
	 //echo $sql_check;
	 $res_check=mysql_query($sql_check);
	 $rowcheck=mysql_num_rows($res_check);
	 //echo $rowcheck;
	// echo $res_check;
	 if($rowcheck){
		 $query_upd= "UPDATE `mdl_i_usercourse_hst` SET  `score` =  $score, `result` =  '$wfresult', `attemp` =  `attemp` + 1, `modified_date` =  '$date', `type` = '$type', `year` = $year, `sem_id`=  '$sem_id' WHERE   user_id = $userid and course_id	= $subj_id and `result` = '".$wfresult."' ; ";
		 //echo $query_upd;
		 $upd_res=mysql_query($query_upd) or die('Unable to update table');
		 
		 }
	 else {
	
		
		$query_ins= "INSERT INTO mdl_i_usercourse_hst (`user_id`,`sem_id`,`course_id`,`score`, `result`, `type`, `year`, attemp, added_date, modified_date)
		VALUES('".$userid."','".$sem_id."','".$subj_id."','".$score."','".$wfresult."','".$type."','".$year."','1','".$date."','".$date."') "; 
		//echo "Insert : ".$query_ins."<br>";
		$ins_res=mysql_query($query_ins) or die('No way man..could not insert');
		$sheet_id=mysql_insert_id();
	 }
	
	}
	
}
	
	}
	
		fclose($fp) or die("can't close file");
		unlink('upload/'.$_FILES["file"]["name"]);
		
   }
		
/*
// echo "<script>location.replace('index.php')</script>";

   }
	  //else 
	  //{
      //   $error = "Error: Username and password combination was incorrect.";
      //}
             
*/
 
   
?>
              <form method="post" enctype="multipart/form-data">
                <table cellpadding="0" cellspacing="0" width="500">
                  <tr>
                    <td width="159">Select Semester</td>
                    <td><select name='sem_list' id='sem_list'>
                    <option value=' '>Select Semester </option>
                        <option value="2">Year 1 Spring</option>
                        <option value="3">Year 1 Fall</option>
                         <option value="1">Year 2 Spring</option>
                          <option value="4">Year 2 Fall</option>
                           <option value="5">Year 3 Spring</option>
                            <option value="6">Year 3 Fall</option>
                            <option value="7">Year 4 Spring</option>
                            <option value="8">Year 4 Fall</option>
                      </select></td>
                  </tr>
                  
                  <tr>
                    <td height="5" colspan="2"></td>
                  </tr>
                  <!--<tr>
                    <td>Select Course</td>
                    <td><div id="txtHint">
                       <select name="subj_list" id="subj_list" onChange="getUserID(this.value)">
                          <option value='0'>Select Subject </option>
                        </select>
                      </div></td>
                  </tr>-->
                  <tr>
                    <td height="5" colspan="2"></td>
                  </tr>
                  <tr>
                    <td>Upload Date</td>
                    <td><input type="text" name="date8a" id="upload_date" readonly />
                      &nbsp;<img src="images/dd-img.gif" alt="" id="date-button-date"/></td>
                  </tr>
                  <tr>
                    <td height="5" colspan="2"></td>
                  </tr>
                  <tr>
                    <td>Upload Scores Sheet</td>
                    <td><input type="file" name="file" id="file" /></td>
                  </tr>
                  <tr>
                    <td height="5" colspan="2"></td>
                  </tr>
                 
                  <tr>
                    <td height="5" colspan="2"></td>
                  </tr>
                  <tr>
                    <td>Semester Type</td>
                    <td><input type ="Radio" name="type" id="fall" value="fall" />
                      Fall
                      <input type ="Radio" name="type" id="spring" value="spring"/>
                      Spring </td>
                  </tr>
                  
                  <tr>
                    <td>Academic Year</td>
                    <td><select name='academic_year' id='academic_year'>
                    	<option value=' '>Year</option>
                        <option value="2010">2010</option>
                        <option value="2011">2011</option>
                        <option value="2012">2012</option>
                      </select></td>
                  </tr>
                  <tr>
                    <td height="5" colspan="2"></td>
                  </tr>
                  
                  <tr>
                    <td height="5" colspan="2"></td>
                  </tr>
                  <tr>
                    <!--<td>
	<select name='result_list' id='result_list'>
	<option value='0'>Select Result</option>
	<option value='pass'>Pass</option>
	<option value='fail'>Fail</option>
	</select>
</td>-->
                    <td colspan="2"><input type = "submit" name="submit" value="Submit" onClick="return checkValidation()" />
                    <?
					if (($ins_res) || ($upd_res))
					echo '<strong>'.'Operation Successfully Completed'.'</strong>'.'<br>';
					?>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" height="5"></td>
                  </tr>
                  <tr>
                    <td colspan="2" ><a href="view_attendance2.php">Click here to view attandance info</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" height="5">
                    
                    This Script inputs the WF records of students at the end of the semester. </td>
                  </tr>
                </table>
              </form>
              <script type="text/javascript">
	<!--  to hide script contents from old browsers
	Zapatec.Calendar.setup({
		inputField     :    "upload_date",     // id of the input field
		ifFormat       :    "%Y-%m-%d",     // format of the input field
		button         :    "date-button-date",  // What will trigger the popup of the calendar
		showsTime      :     false      //don't show time, only date
	});
	// end hiding contents from old browsers  -->
	function checkValidation()
	{
		if(document.getElementById("sem_list").value == '0')
		{
			alert("Please select atleast one semester");
			return false;
		}
		else if(document.getElementById("subj_list").value == '0')
		{
			alert("Please select atleast one subject");
			return false;
		}
		else if(document.getElementById("upload_date").value == '')
		{
			alert("Please select date from calender.")
			return false;
		}
		
		else if(document.getElementById("fall").checked ==  false && document.getElementById("spring").checked == false)
		{
			alert("Please select Semester type.")
			return false;
		}
		
		else if(document.getElementById("academic_year").value == '')
		{
			alert("Please select the Academic year.")
			return false;
		}
		return true;
	}
</script>
            </div></td>
        </tr>
      </table>
    </div>
  </div>
</div>
</body>
</html>