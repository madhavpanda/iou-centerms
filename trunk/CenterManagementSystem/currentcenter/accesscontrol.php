<?
session_start();
if (!session_is_registered('id') || !session_is_registered('adminname'))
{
	header("Location: /admincms/login.php");
}

$acces_type = explode(", ",$_SESSION['access_type']);

if($_SESSION['access_type'] == "all")
{
	// Do nothing
	
}
else
{
	if(!in_array("Center Info", $acces_type))
	{
		header("Location: /admincms/index.php");
	}
}

?>