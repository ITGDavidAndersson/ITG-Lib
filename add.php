<?php
session_name("ITGlib");
session_start();
require("inc/base.php");
if(!isset($_SESSION["books"])) {
	$_SESSION["books"] = [];
}
$msg = "";
if(code::find($_GET["c"])) {
	$pre = false;
	foreach($_SESSION["books"] as $v) {
		if($v === $_GET["c"]) {
			$pre = true;
		}
	}
	if($pre === false) {
		array_push($_SESSION["books"], $_GET["c"]);
		$msg = code::find($_GET["c"])["title"]." har lagts till.";
	} else {
		$msg = "Du har redan skannat denna boken";
	}
} else {
	$msg = "Boken finns inte.";
}
if(!isset($_GET["t"])) {
	header("Location: index.php?msg=".urlencode($msg));
} else {
	echo $msg;
}
?>