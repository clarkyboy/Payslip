<?php
session_start();

if(isset($_GET['do'])) $do = $_GET['do']; else $do = '';


require_once("scripts/connect.php");

$ra_id = isset($_GET['ra_id'])?$_GET['ra_id']:'';
$lf = isset($_GET['lf'])?$_GET['lf']:'';
$_SESSION['tm_law']['ra_id'] = $ra_id;
$_SESSION['tm_law']['ra_name'] = $lf;

	//Redirect back to index page
	header("Location: index.php");
//echo "chosen lawfirm: ".$ra_id ;
//echo "session lawfirm: ".$_SESSION['tm_law']['ra_id'] ;
session_write_close();
?>